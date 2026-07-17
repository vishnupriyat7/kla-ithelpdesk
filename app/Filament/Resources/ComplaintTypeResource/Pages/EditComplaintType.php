<?php

namespace App\Filament\Resources\ComplaintTypeResource\Pages;

use App\Filament\Resources\ComplaintTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintType extends EditRecord
{
    protected static string $resource = ComplaintTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
