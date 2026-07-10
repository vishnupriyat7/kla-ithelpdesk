<?php

namespace App\Filament\Widgets;

use App\Models\ComplaintRegister;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ComplaintRegisterTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets by Complaint Type';
    protected static ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->getRoleName() !== 'chm';
    }

    protected function getData(): array
    {
        $data = ComplaintRegister::select('complaint_type', DB::raw('count(*) as total'))
            ->groupBy('complaint_type')
            ->pluck('total', 'complaint_type')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Complaints',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#ef4444', // Hardware
                        '#3b82f6', // Software
                        '#10b981', // Network
                        '#f59e0b', // Printer
                        '#8b5cf6', // Email
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
