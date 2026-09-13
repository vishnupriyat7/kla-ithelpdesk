<?php

namespace App\Filament\Resources\WhatsappImportStagingResource\Pages;

use App\Filament\Resources\WhatsappImportStagingResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWhatsappImportStaging extends EditRecord
{
    protected static string $resource = WhatsappImportStagingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_now')
                ->label('Import to Ticket')
                ->icon('heroicon-o-arrow-down-on-square')
                ->color('success')
                ->requiresConfirmation()
                ->visible(function () {
                    $role = auth()->user()?->getRoleName();
                    return !$this->record->already_imported && in_array($role, ['superadmin', 'hardwareadmin']);
                })
                ->action(function () {
                    $this->save(); // Save any pending edits first

                    $ticket = WhatsappImportStagingResource::importSingleRecord($this->record);

                    Notification::make()
                        ->title('Ticket Imported Successfully')
                        ->body("Created Ticket #{$ticket->ticket_no} in official Complaint Register.")
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\DeleteAction::make()
                ->visible(fn () => in_array(auth()->user()?->getRoleName(), ['superadmin', 'hardwareadmin', 'admin'])),
        ];
    }
}
