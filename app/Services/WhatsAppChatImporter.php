<?php

namespace App\Services;

use App\Models\ComplaintType;
use App\Models\OfficeLocation;
use App\Models\Room;
use App\Models\Section;
use App\Models\User;
use App\Models\WhatsappImportStaging;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Parses a WhatsApp "chat.md" export into deduplicated rows for the
 * whatsapp_import_stagings table.
 *
 * Core Requirement: One actual complaint from WhatsApp must result in ONE ticket.
 * It traces quote chains (complaint -> hand-raise / assignment -> follow-up / resolution)
 * and merges all messages belonging to the same issue into a single staging record.
 */
class WhatsAppChatImporter
{
    protected const KNOWN_STATUSES = ['Open', 'Assigned', 'Pending', 'Complaint', 'Resolved', 'Closed'];

    /** Sender label (as it appears in the export) => exact or substring of matching users.name */
    protected const SENDER_ALIASES = [
        'shyam kumar kla' => 'Syamkumar',
        'shyam lal kla' => 'SYAMLAL S',
        'nikhil kla' => 'Nikhil E R',
        'maneesh kla' => 'Maneesh Mohan',
        'sruthy kla tech' => 'Sruthi A',
        'umesh gopu kla' => 'Umesh Gopu J R',
        'arun kla malappuram' => 'Arun K V',
        'gopi raj sr kla' => 'Gopiraj M',
        'madhavan sir kla' => 'Madhavan Nair A R',
        'jinza it kla' => 'Ginsa Vaheed',
        'l a sayin it kla' => 'S L Sayin',
        'seena kla' => 'Seena S T',
        'sumi chechi kla' => 'Sumi S',
        'swapna chechi kla' => 'Swapna E V',
        'parvathy kla' => 'Parvathi M',
        'lijesh kumar a' => 'Lijesh Kumar A',
        'you' => 'Vishnupriya T',
    ];

    /** office_locations.location mapping */
    protected const LOCATION_ALIASES = [
        'assembly block' => 'Assembly Block',
        'admin block' => 'Admin Block',
        'administration block' => 'Admin Block',
        'museum' => 'Museum',
        'reception' => 'Reception',
        'nila' => 'Nila',
        'mla hostel nila' => 'Nila',
        'chandragiri' => 'Chandhragiri',
        'chandhragiri' => 'Chandhragiri',
        'periyar' => 'Periyar',
        'neyyar' => 'Neyyar',
    ];

    protected Collection $users;
    protected Collection $technicians;
    protected Collection $officeLocations;
    protected Collection $complaintTypes;
    protected Collection $sections;
    protected Collection $rooms;

    public function __construct()
    {
        $this->users = User::all(['id', 'name', 'role_id']);
        $this->technicians = User::whereHas('role', fn ($q) => $q->whereIn('name', ['chm', 'programmer', 'hardwareadmin']))->get(['id', 'name']);
        $this->officeLocations = OfficeLocation::all(['id', 'location']);
        $this->complaintTypes = ComplaintType::pluck('name');
        $this->sections = Section::pluck('name');
        $this->rooms = Room::all(['id', 'name', 'office_location_id', 'floor_id']);
    }

    /**
     * Parse the .md file and return the grouped staging records.
     */
    public function parseFile(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new \RuntimeException("Could not read file: {$path}");
        }

        $messages = $this->splitIntoMessages($lines);

        return $this->groupMessagesIntoRecords($messages);
    }

    /**
     * Parse and populate the whatsapp_import_stagings table.
     * Leaves rows already marked as already_imported untouched.
     */
    public function stage(string $path): array
    {
        $records = $this->parseFile($path);

        $skippedAlreadyLive = 0;
        $created = 0;

        DB::transaction(function () use ($records, &$skippedAlreadyLive, &$created) {
            // Remove unimported records to allow clean re-parsing
            WhatsappImportStaging::where('already_imported', false)->delete();

            foreach ($records as $record) {
                if (!empty($record['ticket_no']) && DB::table('complaint_tickets')->where('ticket_no', $record['ticket_no'])->exists()) {
                    $skippedAlreadyLive++;
                    continue;
                }

                WhatsappImportStaging::create($record);
                $created++;
            }
        });

        return [
            'created' => $created,
            'skipped_already_live' => $skippedAlreadyLive,
            'total_source_messages' => count($records) ? array_sum(array_column($records, 'merge_count')) : 0,
        ];
    }

    // ------------------------------------------------------------------
    // Step 1: Split raw export into individual messages
    // ------------------------------------------------------------------

    public function splitIntoMessages(array $lines): array
    {
        $currentDate = null;
        $messages = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^## (\d{1,2} [A-Za-z]+ \d{4})/', $line, $m)) {
                $currentDate = $m[1];
                continue;
            }

            if (preg_match('/^\[(\d{1,2}:\d{2})\] \*\*([^*]+):\*\* ?(.*)/', $line, $m)) {
                if ($current) {
                    $messages[] = $current;
                }

                $current = [
                    'date' => $currentDate,
                    'time' => $m[1],
                    'sender' => trim($m[2]),
                    'text' => trim($m[3]),
                ];
                continue;
            }

            if (preg_match('/^__(.+)__$/', $line, $m)) {
                // Ignore system notifications (e.g. "X added Y")
                if ($current) {
                    $messages[] = $current;
                }
                $current = null;
                continue;
            }

            if ($current !== null) {
                $current['text'] .= "\n" . $line;
            }
        }

        if ($current) {
            $messages[] = $current;
        }

        return $messages;
    }

    // ------------------------------------------------------------------
    // Step 2: Quote extraction and backward chain resolution
    // ------------------------------------------------------------------

    protected function extractQuoteInfo(string $text): ?array
    {
        if (!str_starts_with(ltrim($text), '>')) {
            return null;
        }

        $lines = explode("\n", $text);
        $quoteLines = [];
        $author = null;
        $inQuote = false;

        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '>')) {
                $inQuote = true;
                $clean = preg_replace('/^\s*>\s*_?/', '', $line);
                if ($author === null && preg_match('/^([^:]+):\s*(.*)/', $clean, $m)) {
                    $author = trim($m[1]);
                    $clean = $m[2];
                }
                $quoteLines[] = $clean;
                if (str_ends_with(rtrim($clean), '_')) {
                    break;
                }
            } elseif ($inQuote) {
                break;
            }
        }

        $body = trim(implode("\n", $quoteLines), " \t\n\r\0\x0B_");

        return [
            'author' => $author,
            'body' => $body,
        ];
    }

    /**
     * Group raw messages into unified complaint records.
     */
    protected function groupMessagesIntoRecords(array $messages): array
    {
        $parentMap = [];

        for ($i = 0; $i < count($messages); $i++) {
            $info = $this->extractQuoteInfo($messages[$i]['text']);
            if ($info) {
                $qBodyClean = mb_strtolower(trim(preg_replace('/[\p{Emoji_Presentation}\p{Extended_Pictographic}\x{200d}\x{fe0f}]/u', '', $info['body'])));

                for ($b = $i - 1; $b >= 0; $b--) {
                    $prev = $messages[$b];

                    // If quote header specifies author, check sender match
                    if ($info['author'] && mb_stripos($prev['sender'], $info['author']) === false && mb_stripos($info['author'], $prev['sender']) === false) {
                        continue;
                    }

                    $prevWithoutQuote = preg_replace('/^>.*?\n\n?/s', '', trim($prev['text']));
                    $prevClean = mb_strtolower(trim(preg_replace('/[\p{Emoji_Presentation}\p{Extended_Pictographic}\x{200d}\x{fe0f}]/u', '', $prevWithoutQuote)));

                    // Both are emoji acknowledgments (e.g. ✋ or 🙋)
                    if ($qBodyClean === '' && $prevClean === '') {
                        $parentMap[$i] = $b;
                        break;
                    }

                    // Content substring match
                    if ($prevClean !== '' && (mb_stripos($prevClean, $qBodyClean) !== false || mb_stripos($qBodyClean, $prevClean) !== false)) {
                        $parentMap[$i] = $b;
                        break;
                    }

                    if (mb_stripos($prev['text'], $info['body']) !== false) {
                        $parentMap[$i] = $b;
                        break;
                    }
                }
            }
        }

        // Trace root complaint for each message
        $clusters = []; // rootIndex => array of message indices
        for ($i = 0; $i < count($messages); $i++) {
            $curr = $i;
            $visited = [$curr => true];
            while (isset($parentMap[$curr])) {
                $p = $parentMap[$curr];
                if (isset($visited[$p])) {
                    break;
                }
                $visited[$p] = true;
                $curr = $p;
            }
            $clusters[$curr][] = $i;
        }

        // Now transform each cluster into one staging record
        $stagingRecords = [];

        foreach ($clusters as $rootIndex => $indices) {
            if ($this->isNoiseCluster($messages, $indices)) {
                continue;
            }

            $record = $this->buildStagingRecordFromCluster($messages, $indices);
            if ($record !== null) {
                $stagingRecords[] = $record;
            }
        }

        return $stagingRecords;
    }

    protected function isNoiseCluster(array $messages, array $indices): bool
    {
        $allText = '';
        foreach ($indices as $idx) {
            $allText .= ' ' . trim($messages[$idx]['text']);
        }
        $trimmed = trim($allText);
        if ($trimmed === '') {
            return true;
        }

        // Strip known media tags
        $clean = preg_replace('/(This message was deleted|\[unknown message\]|\[Image\]|\[Video\]|\[Sticker\]|\[Voice message\]|\[Document\]|\[album message\])/i', '', $trimmed);
        if (trim($clean) === '') {
            return true;
        }

        // Festival greetings, photo sharing chatter, or admin announcements
        if (preg_match('/(ഓണാശംസകൾ|പൊന്നോണം ആശംസിക്കുന്നു|photo edukkande|Photo edukkan varumo|പൂക്കൾ ധാരാളം അരിയുവാനുണ്ട്|അത്തപ്പൂവിന്റെ അടുത്ത്|വേണം വേണം ഇപ്പോൾ കഴിച്ചു)/iu', $clean)) {
            return true;
        }

        if (preg_match('/^NOTICE\*\s*📌.*staff meeting/is', trim($clean))) {
            return true;
        }

        if (preg_match('/^@.*We need short meeting now/is', trim($clean))) {
            return true;
        }

        if (preg_match('/^Dear all,.*transferred to another section/is', trim($clean))) {
            return true;
        }

        if (preg_match('/^Good morning, everyone\..*UPS battery replacement.*successfully completed/is', trim($clean))) {
            return true;
        }

        return false;
    }

    protected function buildStagingRecordFromCluster(array $messages, array $indices): ?array
    {
        $rootMsg = $messages[$indices[0]];
        $rootText = trim($rootMsg['text']);

        // Check if root message has ticket number
        $ticketNo = $this->extractTicketNo($rootText);

        $record = [
            'ticket_no' => $ticketNo,
            'employee_id' => null,
            'section' => null,
            'office_location_id' => null,
            'floor' => null,
            'room_id' => null,
            'complaint_type' => 'Computer', // default fallback
            'description' => null,
            'status' => 'Open',
            'remarks' => null,
            'vendor_complaint_id' => null,
            'technician_id' => null,
            'user_id' => $this->resolveUserBySender($rootMsg['sender']),
            'reported_at' => $this->parseDateTime($rootMsg['date'], $rootMsg['time']),
            'source_key' => 'WA_' . ($rootMsg['date'] ? date('Ymd', strtotime($rootMsg['date'])) : '00000000') . '_' . str_replace(':', '', $rootMsg['time']) . '_' . $indices[0],
            'raw_messages' => '',
            'merge_count' => count($indices),
            'already_imported' => false,
        ];

        // Format description from the root message (stripping quote if any)
        $cleanRootText = trim($this->stripQuoteBlock($rootText));
        $record['description'] = $cleanRootText !== '' ? $cleanRootText : $rootText;

        // Try extracting structured labels from the root message if present
        $this->extractStructuredFields($record, $cleanRootText);

        $resolutionNotes = [];
        $rawLogs = [];

        // Now process each message in the thread chronologically
        foreach ($indices as $idx) {
            $msg = $messages[$idx];
            $text = trim($msg['text']);
            $body = trim($this->stripQuoteBlock($text));
            $senderTechId = $this->resolveTechnicianBySender($msg['sender']);

            $rawLogs[] = "[{$msg['date']} {$msg['time']}] {$msg['sender']}: {$text}";

            // Look for ticket number if missing
            if (!$record['ticket_no']) {
                $foundTicket = $this->extractTicketNo($text);
                if ($foundTicket) {
                    $record['ticket_no'] = $foundTicket;
                }
            }

            // Look for Vendor / AMC Complaint ID
            $vendorId = $this->extractVendorId($text);
            if ($vendorId) {
                $record['vendor_complaint_id'] = $vendorId;
            }

            // Status & Technician detection
            $detectedStatus = $this->detectStatusFromSymbols($text);

            if ($detectedStatus) {
                if ($detectedStatus === 'Resolved') {
                    $record['status'] = 'Resolved';
                } elseif ($detectedStatus === 'Complaint') {
                    $record['status'] = 'Complaint';
                } elseif ($detectedStatus === 'Pending' && $record['status'] !== 'Resolved') {
                    $record['status'] = 'Pending';
                } elseif ($detectedStatus === 'Assigned' && $record['status'] === 'Open') {
                    $record['status'] = 'Assigned';
                }
            }

            // If this message was from a technician and assigned/resolved, associate technician
            if ($senderTechId) {
                if ($record['technician_id'] === null || in_array($detectedStatus, ['Resolved', 'Complaint', 'Pending'])) {
                    $record['technician_id'] = $senderTechId;
                }
            }

            // Also check for explicit "Resolved by" / "Handled by" text
            $resolverId = $this->extractResolverTechnician($text);
            if ($resolverId) {
                $record['technician_id'] = $resolverId;
            }

            // If message is a follow-up or resolution with content, collect notes
            if ($idx !== $indices[0] && $body !== '') {
                // If it's not just a bare ack emoji
                $withoutEmoji = trim(preg_replace('/[\p{Emoji_Presentation}\p{Extended_Pictographic}\x{200d}\x{fe0f}]/u', '', $body));
                if ($withoutEmoji !== '' && !preg_match('/^reminder$/i', $withoutEmoji)) {
                    $resolutionNotes[] = "[{$msg['sender']}]: {$body}";
                }
            }

            // Try location/section/complaint type extraction if not yet found
            if (!$record['section']) {
                $this->extractSectionAndLocation($record, $body);
            }
            if ($record['complaint_type'] === 'Computer') {
                $record['complaint_type'] = $this->detectComplaintType($text);
            }
        }

        if (!empty($resolutionNotes)) {
            $record['remarks'] = implode("\n", $resolutionNotes);
        }

        $record['raw_messages'] = implode("\n\n", $rawLogs);

        // Final sanity checks on section and complaint type
        if (empty($record['section'])) {
            $record['section'] = $this->guessSection($record['description']);
        }

        return $record;
    }

    // ------------------------------------------------------------------
    // Step 3: Field extraction helpers
    // ------------------------------------------------------------------

    protected function extractStructuredFields(array &$record, string $text): void
    {
        if ($val = $this->extractLabel($text, ['Section'])) {
            $record['section'] = $val;
        }
        if ($val = $this->extractLabel($text, ['Requested By'])) {
            $record['employee_id'] = $val;
        }
        if ($val = $this->extractLabel($text, ['Location', 'Location / Room'])) {
            $this->parseLocationString($record, $val);
        }
        if ($val = $this->extractLabel($text, ['Type', 'Complaint Type'])) {
            $record['complaint_type'] = $this->matchComplaintType($val);
        }
        if ($val = $this->extractLabel($text, ['Problem', 'Description'])) {
            $record['description'] = $val;
        }
        if ($val = $this->extractLabel($text, ['Vendor ID', 'Vendor', 'AMC Complaint ID'])) {
            $record['vendor_complaint_id'] = $val;
        }
    }

    protected function extractSectionAndLocation(array &$record, string $text): void
    {
        $lower = mb_strtolower($text);

        // Check for known locations
        foreach (self::LOCATION_ALIASES as $alias => $canonical) {
            if (str_contains($lower, $alias)) {
                $loc = $this->officeLocations->firstWhere('location', $canonical);
                if ($loc) {
                    $record['office_location_id'] = $loc->id;
                    break;
                }
            }
        }

        // Check for room number, e.g. "Room no.822", "Room - 213", "Room 504", "B 44", "B-46", "713", "813"
        if (preg_match('/(?:Room\s*(?:no\.?|–|-)?\s*|Rm\s*-?\s*|B-?)([A-Z]?\d{2,4}[A-Za-z]?)/i', $text, $m)) {
            $roomNum = trim($m[1]);
            $matchedRoom = $this->rooms->first(fn ($r) => mb_stripos($r->name, $roomNum) !== false);
            if ($matchedRoom) {
                $record['room_id'] = $matchedRoom->id;
                if (!$record['office_location_id']) {
                    $record['office_location_id'] = $matchedRoom->office_location_id;
                }
            } else {
                $record['floor'] = 'Room ' . $roomNum;
            }
        }

        // Check for Section
        foreach ($this->sections as $sec) {
            if (mb_stripos($text, $sec) !== false) {
                $record['section'] = $sec;
                break;
            }
        }
    }

    protected function guessSection(string $text): string
    {
        $lines = explode("\n", $text);
        $first = trim($lines[0]);

        // "Editing Room no.822" -> Section: "Editing"
        // "CAD -DS Network issue" -> Section: "CAD"
        // "Table : Deepa - Pressbox Issue" -> Section: "Table"
        foreach ([' - ', '–', ':', '-'] as $sep) {
            if (str_contains($first, $sep)) {
                $candidate = trim(explode($sep, $first, 2)[0]);
                if (mb_strlen($candidate) >= 2 && mb_strlen($candidate) <= 30 && !preg_match('/^\d+$/', $candidate)) {
                    return $candidate;
                }
            }
        }

        $words = preg_split('/\s+/', $first);
        if (count($words) > 0 && mb_strlen($words[0]) > 2) {
            return $words[0];
        }

        return 'IT Section';
    }

    protected function detectComplaintType(string $text): string
    {
        $lower = mb_strtolower($text);

        if (preg_match('/(printer|toner|cartridge|printing|print|xerox|lipi|brother|paper jam)/i', $lower)) {
            return 'Printer';
        }
        if (preg_match('/(scanner|scanning|scan)/i', $lower)) {
            return 'Scanner';
        }
        if (preg_match('/(e-office|eoffice)/i', $lower)) {
            return 'e-Office';
        }
        if (preg_match('/(attendance app|punching|kiosk)/i', $lower)) {
            return 'Attendance App';
        }
        if (preg_match('/(network|wifi|lan|intranet|internet|port|ftp)/i', $lower)) {
            return 'Network';
        }
        if (preg_match('/(sabha tv|tv)/i', $lower)) {
            return 'TV';
        }
        if (preg_match('/(libreoffice|chrome|pagemaker|vlc|dms|spark|pinta|font|manglish|typeit|software)/i', $lower)) {
            return 'Software';
        }
        if (preg_match('/(presentation|hall|meeting|cm program)/i', $lower)) {
            return 'Assistance to Official Meetings';
        }

        return 'Computer';
    }

    protected function extractVendorId(string $text): ?string
    {
        if (preg_match('/(?:AMC\s*(?:Complaint\s*)?ID|IHRD)\s*[:–-]?\s*(\d{4,6})/i', $text, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function extractTicketNo(string $text): ?string
    {
        if (preg_match_all('/IT-\d{6,}/i', $text, $matches)) {
            return strtoupper(end($matches[0]));
        }

        return null;
    }

    protected function extractLabel(string $text, array $labels): ?string
    {
        $labelPattern = implode('|', array_map(fn ($l) => preg_quote($l, '/'), $labels));
        if (preg_match('/(?:' . $labelPattern . ')\s*:\s*([^\n]+)/i', $text, $m)) {
            return trim($m[1], " \t\n\r\0\x0B_*");
        }

        return null;
    }

    protected function matchComplaintType(string $raw): string
    {
        $match = $this->complaintTypes->first(fn ($name) => mb_strtolower($name) === mb_strtolower(trim($raw)));

        return $match ?? trim($raw);
    }

    protected function parseLocationString(array &$record, string $raw): void
    {
        $parts = array_map('trim', explode('/', $raw));
        $head = mb_strtolower($parts[0] ?? '');
        $canonical = self::LOCATION_ALIASES[$head] ?? null;

        $location = $canonical
            ? $this->officeLocations->firstWhere('location', $canonical)
            : $this->officeLocations->first(fn ($loc) => mb_strtolower($loc->location) === $head);

        if ($location) {
            $record['office_location_id'] = $location->id;
            if (isset($parts[1])) {
                $record['floor'] = $parts[1];
            }
            if (isset($parts[2])) {
                $roomName = $parts[2];
                $room = Room::where('office_location_id', $location->id)->where('name', 'like', "%{$roomName}%")->first();
                if ($room) {
                    $record['room_id'] = $room->id;
                }
            }
        }
    }

    protected function detectStatusFromSymbols(string $text): ?string
    {
        $lower = mb_strtolower($text);

        if (str_contains($text, '✅') || str_contains($lower, 'resolved') || str_contains($lower, 'rectified') || str_contains($lower, 'solved') || str_contains($lower, 'done')) {
            return 'Resolved';
        }
        if (str_contains($lower, 'amc complaint id') || str_contains($lower, 'amc id') || str_contains($lower, 'ihrd :')) {
            return 'Complaint';
        }
        if (str_contains($text, '❌') || str_contains($lower, 'pending') || str_contains($lower, 'taken to it') || str_contains($lower, 'taken for service')) {
            return 'Pending';
        }
        if (str_contains($text, '🙋') || str_contains($text, '✋') || str_contains($lower, 'assigned')) {
            return 'Assigned';
        }

        return null;
    }

    protected function stripQuoteBlock(string $text): string
    {
        if (!str_starts_with(ltrim($text), '>')) {
            return $text;
        }

        $lines = explode("\n", $text);
        $i = 0;
        $inQuote = false;

        foreach ($lines as $idx => $line) {
            if ($idx === 0) {
                $inQuote = true;
            }
            if ($inQuote && str_ends_with(rtrim($line), '_')) {
                $i = $idx + 1;
                break;
            }
        }

        return implode("\n", array_slice($lines, $i));
    }

    protected function resolveUserBySender(string $sender): ?int
    {
        $key = mb_strtolower(trim($sender));
        $aliasName = self::SENDER_ALIASES[$key] ?? null;

        if (!$aliasName) {
            $aliasName = $sender;
        }

        $user = $this->users->first(fn ($u) => mb_strtolower($u->name) === mb_strtolower($aliasName))
            ?? $this->users->first(fn ($u) => mb_stripos($u->name, $aliasName) !== false || mb_stripos($aliasName, $u->name) !== false);

        return $user?->id;
    }

    protected function resolveTechnicianBySender(string $sender): ?int
    {
        $key = mb_strtolower(trim($sender));
        $aliasName = self::SENDER_ALIASES[$key] ?? $sender;

        $tech = $this->technicians->first(fn ($u) => mb_strtolower($u->name) === mb_strtolower($aliasName))
            ?? $this->technicians->first(fn ($u) => mb_stripos($u->name, $aliasName) !== false || mb_stripos($aliasName, $u->name) !== false)
            ?? $this->users->first(fn ($u) => mb_strtolower($u->name) === mb_strtolower($aliasName))
            ?? $this->users->first(fn ($u) => mb_stripos($u->name, $aliasName) !== false || mb_stripos($aliasName, $u->name) !== false);

        return $tech?->id;
    }

    protected function extractResolverTechnician(string $text): ?int
    {
        if (preg_match('/(?:Resolved|Rectified|Done|Assistance given)\s*(?:by|\+|-)?\s*([A-Za-z\s,&+]+)/i', $text, $m)) {
            $names = preg_split('/[\s,&+]+/', trim($m[1]));
            foreach ($names as $name) {
                $name = trim($name);
                if (mb_strlen($name) < 3) continue;
                $tech = $this->technicians->first(fn ($u) => mb_stripos($u->name, $name) !== false);
                if ($tech) {
                    return $tech->id;
                }
            }
        }

        return null;
    }

    protected function parseDateTime(?string $date, ?string $time): ?Carbon
    {
        if (!$date || !$time) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d F Y H:i', "{$date} {$time}");
        } catch (\Exception $e) {
            return null;
        }
    }
}
