<?php

namespace App\Filament\Widgets;

use App\Models\ComplaintRegister;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ComplaintRegisterStatsOverview extends BaseWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $role = auth()->user()->getRoleName();
        $userId = auth()->id();

        // 1. GLOBAL STATS (Shown to everyone)
        $gTotal = ComplaintRegister::count();
        $gOpen = ComplaintRegister::where('status', 'Open')->count();
        $gAssigned = ComplaintRegister::where('status', 'Assigned')->count();
        $gPending = ComplaintRegister::where('status', 'Pending')->count();
        $gComplaint = ComplaintRegister::where('status', 'Complaint')->count();
        $gResolvedTotal = ComplaintRegister::where('status', 'Resolved')->count();
        
        $gResolved = ComplaintRegister::where('status', 'Resolved')->whereDate('updated_at', today())->count();

        $globalBadges = new \Illuminate\Support\HtmlString('
            <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; width: 100%;">
                <div style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Open</span>
                    <strong style="font-size: 0.875rem;">' . $gOpen . '</strong>
                </div>
                <div style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Assigned</span>
                    <strong style="font-size: 0.875rem;">' . $gAssigned . '</strong>
                </div>
                <div style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Pending</span>
                    <strong style="font-size: 0.875rem;">' . $gPending . '</strong>
                </div>
                <div style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Complaint</span>
                    <strong style="font-size: 0.875rem;">' . $gComplaint . '</strong>
                </div>
                <div style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Resolved</span>
                    <strong style="font-size: 0.875rem;">' . $gResolvedTotal . '</strong>
                </div>
            </div>
        ');

        $stats = [
            Stat::make('Total TicketCount', $gTotal)
                ->description($globalBadges)
                ->color('primary'),
                
            Stat::make('Total Open Tickets', $gOpen)
                ->description(new \Illuminate\Support\HtmlString('<a href="' . url('/admin/complaint-live-screen') . '" style="color: blue; text-decoration: underline;">Go to Live Board</a>'))
                ->color('warning'),
                
            Stat::make('Resolved Today', $gResolved)
                ->description('Tickets resolved in the last 24 hours')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Vendor Complaints', \App\Models\VendorComplaint::count())
                ->description(new \Illuminate\Support\HtmlString(
                    '<span style="color: #ef4444;">Un attended: ' . \App\Models\VendorComplaint::where('status', 'Un attended')->count() . '</span> | ' .
                    '<span style="color: #f59e0b;">Pending Spare: ' . \App\Models\VendorComplaint::where('status', 'Pending Spare')->count() . '</span> | ' .
                    '<span style="color: #22c55e;">Resolved: ' . \App\Models\VendorComplaint::where('status', 'Resolved')->count() . '</span>'
                ))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];

        // 2. PERSONAL STATS (Only for technicians)
        if (!in_array($role, ['superadmin', 'hardwareadmin', 'admin'])) {
            $myQuery = ComplaintRegister::where('technician_id', $userId);
            
            $mTotal = (clone $myQuery)->count();
            $mOpen = (clone $myQuery)->where('status', 'Open')->count();
            $mAssigned = (clone $myQuery)->where('status', 'Assigned')->count();
            $mPending = (clone $myQuery)->where('status', 'Pending')->count();
            $mComplaint = (clone $myQuery)->where('status', 'Complaint')->count();
            $mResolved = (clone $myQuery)->where('status', 'Resolved')->count();

            $myBadges = new \Illuminate\Support\HtmlString('
                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; width: 100%;">
                    <div style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Assigned</span>
                        <strong style="font-size: 0.875rem;">' . $mAssigned . '</strong>
                    </div>
                    <div style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Pending</span>
                        <strong style="font-size: 0.875rem;">' . $mPending . '</strong>
                    </div>
                    <div style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Complaint</span>
                        <strong style="font-size: 0.875rem;">' . $mComplaint . '</strong>
                    </div>
                    <div style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Resolved</span>
                        <strong style="font-size: 0.875rem;">' . $mResolved . '</strong>
                    </div>
                </div>
            ');

            $stats[] = Stat::make('My Tickets', $mTotal)
                ->description($myBadges)
                ->color('info');
        }

        return $stats;
    }
}
