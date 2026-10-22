<?php

namespace App\Filament\Widgets;

use App\Models\ComplaintRegister;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ComplaintRegisterTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets by Complaint Type';
    protected static ?string $maxHeight = '440px';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->getRoleName() !== 'chm';
    }

    protected function getData(): array
    {
        $data = ComplaintRegister::select('complaint_type', DB::raw('count(*) as total'))
            ->groupBy('complaint_type')
            ->orderByDesc('total')
            ->pluck('total', 'complaint_type')
            ->toArray();

        // One distinct colour per type so no two slices share a colour
        $palette = [
            '#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
            '#6366f1', '#84cc16', '#06b6d4', '#e11d48', '#a855f7', '#22c55e', '#eab308', '#0ea5e9',
            '#d946ef', '#64748b', '#b45309', '#15803d', '#1d4ed8', '#be123c', '#7c3aed', '#0f766e',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Complaints',
                    'data' => array_values($data),
                    'backgroundColor' => array_map(
                        fn (int $i): string => $palette[$i % count($palette)],
                        array_keys(array_values($data))
                    ),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getOptions(): array
    {
        return [
            // A doughnut has no axes; without this the default x/y grid is drawn behind it
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 12,
                        'boxHeight' => 12,
                        'padding' => 12,
                        'font' => ['size' => 13],
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
