<?php

namespace App\Console\Commands;

use App\Filament\Resources\WhatsappImportStagingResource;
use App\Models\WhatsappImportStaging;
use App\Services\WhatsAppChatImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportWhatsAppComplaints extends Command
{
    protected $signature = 'import:whatsapp-complaints 
                            {file : Path to the chat.md file}
                            {--import-now : Directly insert into complaint_tickets table bypassing review}';

    protected $description = 'Parse and stage WhatsApp chat exports for review before inserting into ComplaintRegister';

    public function handle(WhatsAppChatImporter $importer)
    {
        $file = $this->argument('file');

        if (!File::exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Parsing WhatsApp chat export: {$file}");

        $result = $importer->stage($file);

        $this->info("Staging completed successfully!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Source Messages Parsed', $result['total_source_messages']],
                ['Deduplicated Complaints Staged', $result['created']],
                ['Skipped (Already Live in Tickets)', $result['skipped_already_live']],
            ]
        );

        if ($this->option('import-now')) {
            $this->warn("Option --import-now was provided. Inserting staged complaints directly into complaint_tickets...");
            
            $pending = WhatsappImportStaging::where('already_imported', false)->get();
            $bar = $this->output->createProgressBar($pending->count());
            $bar->start();

            $imported = 0;
            foreach ($pending as $record) {
                WhatsappImportStagingResource::importSingleRecord($record);
                $imported++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully imported {$imported} complaints into complaint_tickets table.");
        } else {
            $this->info("Complaints are safely staged in the 'whatsapp_import_stagings' table.");
            $this->info("Please log in to the admin panel at /admin/whatsapp-import-stagings to review, edit, select CHM & status, and import.");
        }

        return 0;
    }
}
