<?php

namespace App\Filament\Resources\ComplaintRegisterResource\Pages;

use App\Filament\Resources\ComplaintRegisterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintRegister extends EditRecord
{
    protected static string $resource = ComplaintRegisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected $technicianAssigned = false;
    protected $newTechnicianId = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldTechnicianId = $this->record->technician_id;
        
        if (isset($data['technician_id']) && $data['technician_id'] != $oldTechnicianId) {
            $this->technicianAssigned = true;
            $this->newTechnicianId = $data['technician_id'];
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->technicianAssigned) {
            if ($this->record->status === 'Open') {
                $this->record->update(['status' => 'Assigned']);
            }

            $technician = \App\Models\User::find($this->newTechnicianId);
            if ($technician) {
                \Filament\Notifications\Notification::make()
                    ->title('Ticket Assigned to You')
                    ->body("Ticket {$this->record->ticket_no} has been assigned to you.")
                    ->success()
                    ->sendToDatabase($technician);
            }
        }
    }
}
