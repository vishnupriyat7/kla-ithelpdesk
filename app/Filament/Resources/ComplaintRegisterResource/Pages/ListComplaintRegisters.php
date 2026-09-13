<?php

namespace App\Filament\Resources\ComplaintRegisterResource\Pages;

use App\Exports\ComplaintRegisterExport;
use App\Filament\Resources\ComplaintRegisterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListComplaintRegisters extends ListRecords
{
    protected static string $resource = ComplaintRegisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ActionGroup::make([
                Actions\Action::make('export_excel')
                    ->label('Export to Excel (.xlsx)')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->action(function () {
                        $records = $this->getFilteredTableQuery()->get();
                        $filename = 'Complaint_Register_' . date('Y-m-d_His') . '.xlsx';
                        return Excel::download(new ComplaintRegisterExport($records), $filename);
                    }),
                Actions\Action::make('export_csv')
                    ->label('Export to CSV (.csv)')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->action(function () {
                        $records = $this->getFilteredTableQuery()->get();
                        $filename = 'Complaint_Register_' . date('Y-m-d_His') . '.csv';
                        return Excel::download(new ComplaintRegisterExport($records), $filename, \Maatwebsite\Excel\Excel::CSV);
                    }),
            ])
            ->label('Export')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->button(),
        ];
    }
}
