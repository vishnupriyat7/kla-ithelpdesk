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

    protected function afterCreate(): void
    {
        $admins = \App\Models\User::whereHas('role', function ($query) {
            $query->whereIn('name', ['superadmin', 'admin', 'hardwareadmin']);
        })->get();

        foreach ($admins as $admin) {
            \Filament\Notifications\Notification::make()
                ->title('New Complaint Raised')
                ->body("A new ticket ({$this->record->ticket_no}) has been raised and is Open.")
                ->warning()
                ->sendToDatabase($admin);
        }
    }
}
