<?php

namespace App\Filament\Resources\ComplaintRegisterResource\Pages;

use App\Filament\Resources\ComplaintRegisterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateComplaintRegister extends CreateRecord
{
    protected static string $resource = ComplaintRegisterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['technician_id'])) {
            $data['status'] = 'Assigned';
        } else {
            $data['status'] = 'Open';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $admins = \App\Models\User::whereHas('role', function ($query) {
            $query->whereIn('name', ['superadmin', 'admin', 'hardwareadmin']);
        })->get();

        foreach ($admins as $admin) {
            \Filament\Notifications\Notification::make()
                ->title('New Complaint Raised')
                ->body("A new ticket ({$this->record->ticket_no}) has been raised and is " . $this->record->status . ".")
                ->warning()
                ->sendToDatabase($admin);
        }

        // If a technician was assigned during creation, notify them directly
        if (!empty($this->record->technician_id)) {
            $technician = \App\Models\User::find($this->record->technician_id);
            if ($technician) {
                \Filament\Notifications\Notification::make()
                    ->title('Ticket Assigned to You')
                    ->body("Ticket {$this->record->ticket_no} has been assigned to you upon creation.")
                    ->success()
                    ->sendToDatabase($technician);
            }
        }
    }
}
