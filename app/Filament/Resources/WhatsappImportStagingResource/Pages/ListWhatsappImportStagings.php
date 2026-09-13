<?php

namespace App\Filament\Resources\WhatsappImportStagingResource\Pages;

use App\Filament\Resources\WhatsappImportStagingResource;
use App\Models\WhatsappImportStaging;
use App\Services\WhatsAppChatImporter;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListWhatsappImportStagings extends ListRecords
{
    protected static string $resource = WhatsappImportStagingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Parse & Sync Action (Upload chat.md or parse from server Downloads)
            Actions\Action::make('sync_from_chat')
                ->label('Parse / Sync WhatsApp Chat')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->modalHeading('Parse WhatsApp Chat Export into Staging')
                ->modalDescription('Upload a chat.md file or use the existing export in Downloads. Complaints will be deduplicated and staged for review without creating tickets yet.')
                ->visible(fn () => in_array(auth()->user()?->getRoleName(), ['superadmin', 'hardwareadmin']))
                ->form([
                    Forms\Components\FileUpload::make('chat_file')
                        ->label('Upload chat.md from your computer')
                        ->acceptedFileTypes(['text/markdown', 'text/plain', '.md', '.txt'])
                        ->disk('local')
                        ->directory('whatsapp_chat_uploads')
                        ->helperText('Select your exported chat.md file to parse.'),
                    Forms\Components\TextInput::make('server_file_path')
                        ->label('Or use file path on server')
                        ->default('/home/kla/Downloads/KLA IT - Official-chat-17-08-26 to 13-09-26 (1)/chat.md')
                        ->helperText('Used if no file is uploaded above.'),
                ])
                ->action(function (array $data) {
                    $path = null;
                    if (!empty($data['chat_file'])) {
                        $path = Storage::disk('local')->path($data['chat_file']);
                    } elseif (!empty($data['server_file_path'])) {
                        $path = $data['server_file_path'];
                    }

                    if (!$path || !file_exists($path)) {
                        Notification::make()
                            ->title('File Not Found')
                            ->body("Could not find the chat export file at: " . ($path ?? 'N/A'))
                            ->danger()
                            ->send();
                        return;
                    }

                    $importer = new WhatsAppChatImporter();
                    $res = $importer->stage($path);

                    Notification::make()
                        ->title('WhatsApp Chat Staged Successfully')
                        ->body("Parsed {$res['total_source_messages']} messages into {$res['created']} deduplicated complaints ready for review.")
                        ->success()
                        ->send();
                }),

            // 2. Import All Pending Complaints (Super Admin and Hardware Admin only)
            Actions\Action::make('import_all_pending')
                ->label('Import All Pending to Tickets')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Import All Pending Staged Complaints')
                ->modalDescription('This will insert all currently unimported staged complaints into the official Complaint Register. Each complaint will create exactly ONE ticket with the assigned CHM technician and status.')
                ->visible(function () {
                    $role = auth()->user()?->getRoleName();
                    return in_array($role, ['superadmin', 'hardwareadmin']) && WhatsappImportStaging::where('already_imported', false)->exists();
                })
                ->action(function () {
                    $role = auth()->user()?->getRoleName();
                    if (!in_array($role, ['superadmin', 'hardwareadmin'])) {
                        Notification::make()
                            ->title('Unauthorized')
                            ->body('Only Super Admin and Hardware Admin are permitted to import complaints.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $pendingRecords = WhatsappImportStaging::where('already_imported', false)->get();
                    $importedCount = 0;

                    DB::transaction(function () use ($pendingRecords, &$importedCount) {
                        foreach ($pendingRecords as $record) {
                            WhatsappImportStagingResource::importSingleRecord($record);
                            $importedCount++;
                        }
                    });

                    Notification::make()
                        ->title('Import Completed')
                        ->body("{$importedCount} complaints successfully imported into official Complaint Register.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pending Import')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('already_imported', false))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('already_imported', false)->count())
                ->badgeColor('warning'),
            'imported' => Tab::make('Imported')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('already_imported', true))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('already_imported', true)->count())
                ->badgeColor('success'),
            'all' => Tab::make('All Staged')
                ->badge(fn () => static::getResource()::getEloquentQuery()->count()),
        ];
    }
}
