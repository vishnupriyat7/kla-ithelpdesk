<?php

namespace App\Filament\Resources\VendorComplaintResource\Pages;

use App\Filament\Resources\VendorComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorComplaint extends EditRecord
{
    protected static string $resource = VendorComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => auth()->user()?->getRoleName() !== 'superadmin'),
        ];
    }
}
