<?php

namespace App\Exports;

use App\Models\ComplaintRegister;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintRegisterExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected mixed $records = null;

    public function __construct(mixed $records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        if ($this->records instanceof Collection) {
            return $this->records->loadMissing(['technician', 'location', 'room', 'raisedBy']);
        }

        if ($this->records instanceof \Illuminate\Database\Eloquent\Builder) {
            return $this->records->with(['technician', 'location', 'room', 'raisedBy'])->get();
        }

        $query = ComplaintRegister::query();

        if (auth()->check()) {
            $role = auth()->user()->getRoleName();
            if (in_array($role, ['chm', 'programmer'])) {
                $query->where(function ($q) {
                    $q->where('status', 'Open')
                      ->orWhere('technician_id', auth()->id());
                });
            } elseif (!in_array($role, ['admin', 'superadmin', 'hardwareadmin', 'cowd'])) {
                $query->where('technician_id', auth()->id());
            }
        }

        return $query->with(['technician', 'location', 'room', 'raisedBy'])
            ->latest('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Ticket No',
            'Date & Time',
            'Section',
            'Location',
            'Floor',
            'Room',
            'Complaint Type',
            'Description',
            'Status',
            'Assigned CHM',
            'Remarks',
            'AMC / Vendor Complaint ID',
            'Reported By',
        ];
    }

    /**
     * @param ComplaintRegister $ticket
     */
    public function map($ticket): array
    {
        $reportedBy = '';
        if (!empty($ticket->employee_id)) {
            $reportedBy = \App\Filament\Resources\ComplaintRegisterResource::resolveEmployeeName($ticket->employee_id);
            if ($reportedBy === '-') {
                $reportedBy = $ticket->employee_id;
            }
        }
        if (empty($reportedBy) || $reportedBy === '-') {
            $reportedBy = $ticket->raisedBy?->name ?? '';
        }

        return [
            $ticket->ticket_no,
            $ticket->created_at ? $ticket->created_at->format('d-m-Y h:i A') : '',
            $ticket->section ?? '',
            $ticket->location ? $ticket->location->location : '',
            $ticket->floor ?? '',
            $ticket->room ? $ticket->room->name : '',
            $ticket->complaint_type ?? '',
            $ticket->description ?? '',
            $ticket->status ?? '',
            $ticket->technician ? $ticket->technician->name : 'Unassigned',
            $ticket->remarks ?? '',
            $ticket->vendor_complaint_id ?? '',
            $reportedBy,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '1E293B'], // Dark slate
                ],
            ],
        ];
    }
}
