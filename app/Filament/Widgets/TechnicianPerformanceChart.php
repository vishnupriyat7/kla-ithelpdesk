<?php

namespace App\Filament\Widgets;

use App\Models\ComplaintRegister;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TechnicianPerformanceChart extends ChartWidget
{
    protected static ?string $heading = 'Resolved Tickets by Technician';
    protected static ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->getRoleName() !== 'chm';
    }

    protected function getData(): array
    {
        $data = ComplaintRegister::where('status', 'Resolved')
            ->join('users', 'complaint_tickets.technician_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(*) as total'))
            ->groupBy('users.name')
            ->pluck('total', 'users.name')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Resolved Tickets',
                    'data' => array_values($data),
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
