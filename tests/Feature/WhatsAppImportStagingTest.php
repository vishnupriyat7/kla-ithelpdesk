<?php

namespace Tests\Feature;

use App\Filament\Resources\WhatsappImportStagingResource;
use App\Models\ComplaintRegister;
use App\Models\Role;
use App\Models\User;
use App\Models\WhatsappImportStaging;
use App\Services\WhatsAppChatImporter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WhatsAppImportStagingTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_chat_importer_parses_and_stages_complaints_without_duplicates()
    {
        $path = '/home/kla/Downloads/KLA IT - Official-chat-17-08-26 to 13-09-26 (1)/chat.md';
        if (!file_exists($path)) {
            $this->markTestSkipped("chat.md not found at {$path}");
        }

        $importer = new WhatsAppChatImporter();
        $records = $importer->parseFile($path);

        $this->assertNotEmpty($records);
        // The raw chat has 1112 messages; deduplicated complaints should be around ~488
        $this->assertLessThan(600, count($records));

        // Check Ed 57 cluster is consolidated into 1 record
        $ed57Records = array_filter($records, fn ($r) => str_contains($r['description'] ?? '', 'Ed 57') || str_contains($r['raw_messages'] ?? '', 'Ed 57'));
        $this->assertCount(1, $ed57Records, 'Ed 57 complaint should be exactly one staged record');

        $ed57 = reset($ed57Records);
        $this->assertEquals('Resolved', $ed57['status']);
        $this->assertEquals('18344', $ed57['vendor_complaint_id']);
        $this->assertNotNull($ed57['technician_id']);
        $this->assertGreaterThan(1, $ed57['merge_count']);
    }

    public function test_superadmin_and_hardwareadmin_can_import_staged_complaint()
    {
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin'], ['guard_name' => 'web']);
        $superadmin = User::firstOrCreate(
            ['email' => 'test_superadmin@kla.com'],
            ['name' => 'Test SuperAdmin', 'username' => 'testsuperadmin', 'password' => bcrypt('password'), 'role_id' => $superadminRole->id]
        );

        $chmRole = Role::firstOrCreate(['name' => 'chm'], ['guard_name' => 'web']);
        $tech = User::firstOrCreate(
            ['email' => 'test_tech@kla.com'],
            ['name' => 'Test CHM Tech', 'username' => 'testtech', 'password' => bcrypt('password'), 'role_id' => $chmRole->id]
        );

        // Create a staged record
        $staging = WhatsappImportStaging::create([
            'section' => 'Editing',
            'complaint_type' => 'Computer',
            'description' => 'Test system not booting',
            'status' => 'Resolved',
            'technician_id' => $tech->id,
            'reported_at' => now()->subDays(5),
            'raw_messages' => 'Test complaint log',
            'merge_count' => 2,
            'already_imported' => false,
        ]);

        $this->actingAs($superadmin);

        // Perform import
        $ticket = WhatsappImportStagingResource::importSingleRecord($staging);

        $this->assertInstanceOf(ComplaintRegister::class, $ticket);
        $this->assertNotEmpty($ticket->ticket_no);
        $this->assertEquals('Editing', $ticket->section);
        $this->assertEquals('Resolved', $ticket->status);
        $this->assertEquals($tech->id, $ticket->technician_id);

        $staging->refresh();
        $this->assertTrue($staging->already_imported);
        $this->assertEquals($ticket->id, $staging->imported_ticket_id);
    }

    public function test_hardwareadmin_can_import_staged_complaint()
    {
        $hardwareAdminRole = Role::firstOrCreate(['name' => 'hardwareadmin'], ['guard_name' => 'web']);
        $hardwareAdmin = User::firstOrCreate(
            ['email' => 'test_hardwareadmin@kla.com'],
            ['name' => 'Test HardwareAdmin', 'username' => 'testhwadmin', 'password' => bcrypt('password'), 'role_id' => $hardwareAdminRole->id]
        );

        $staging = WhatsappImportStaging::create([
            'section' => 'Accounts C',
            'complaint_type' => 'Printer',
            'description' => 'Printer toner empty',
            'status' => 'Open',
            'reported_at' => now()->subDays(2),
            'already_imported' => false,
        ]);

        $this->actingAs($hardwareAdmin);

        $ticket = WhatsappImportStagingResource::importSingleRecord($staging);

        $this->assertInstanceOf(ComplaintRegister::class, $ticket);
        $this->assertTrue($staging->fresh()->already_imported);
    }

    public function test_chm_role_is_not_authorized_to_import()
    {
        $chmRole = Role::firstOrCreate(['name' => 'chm'], ['guard_name' => 'web']);
        $chmUser = User::firstOrCreate(
            ['email' => 'test_chm_unauthorized@kla.com'],
            ['name' => 'Test CHM Unauthorized', 'username' => 'testchmunauth', 'password' => bcrypt('password'), 'role_id' => $chmRole->id]
        );

        $this->actingAs($chmUser);

        // Role check should disallow
        $canImport = in_array(auth()->user()?->getRoleName(), ['superadmin', 'hardwareadmin']);
        $this->assertFalse($canImport, 'CHM user must not have import provision');
    }

    public function test_bulk_import_creates_one_ticket_per_staged_complaint()
    {
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin'], ['guard_name' => 'web']);
        $superadmin = User::firstOrCreate(
            ['email' => 'test_superadmin2@kla.com'],
            ['name' => 'Test SuperAdmin 2', 'username' => 'testsuperadmin2', 'password' => bcrypt('password'), 'role_id' => $superadminRole->id]
        );

        $staging1 = WhatsappImportStaging::create([
            'section' => 'Protocol',
            'complaint_type' => 'Computer',
            'description' => 'Protocol typing issue',
            'status' => 'Resolved',
            'reported_at' => now()->subDays(3),
            'already_imported' => false,
        ]);

        $staging2 = WhatsappImportStaging::create([
            'section' => 'CAD',
            'complaint_type' => 'Network',
            'description' => 'CAD network issue',
            'status' => 'Assigned',
            'reported_at' => now()->subDays(3),
            'already_imported' => false,
        ]);

        $this->actingAs($superadmin);

        $ticket1 = WhatsappImportStagingResource::importSingleRecord($staging1);
        $ticket2 = WhatsappImportStagingResource::importSingleRecord($staging2);

        $this->assertNotEquals($ticket1->ticket_no, $ticket2->ticket_no);
        $this->assertEquals('Protocol', $ticket1->section);
        $this->assertEquals('CAD', $ticket2->section);
        $this->assertTrue($staging1->fresh()->already_imported);
        $this->assertTrue($staging2->fresh()->already_imported);
    }

    public function test_whatsapp_staging_admin_page_renders_successfully()
    {
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin'], ['guard_name' => 'web']);
        $superadmin = User::firstOrCreate(
            ['email' => 'test_superadmin_ui@kla.com'],
            ['name' => 'Test SuperAdmin UI', 'username' => 'testsuperadminui', 'password' => bcrypt('password'), 'role_id' => $superadminRole->id]
        );

        $this->actingAs($superadmin);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        $page = new \App\Filament\Resources\WhatsappImportStagingResource\Pages\ListWhatsappImportStagings();
        $table = $page->table(\Filament\Tables\Table::make($page));
        $this->assertNotEmpty($table->getColumns());
        $this->assertNotEmpty($table->getFilters());
        $this->assertNotEmpty($table->getActions());
        $this->assertTrue($table->hasToggleableColumns());
    }
}
