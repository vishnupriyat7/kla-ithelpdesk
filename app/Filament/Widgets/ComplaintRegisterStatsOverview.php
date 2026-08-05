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

        $url = url('/admin/complaint-live-screen');
        $globalBadges = new \Illuminate\Support\HtmlString('
            <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; width: 100%;">
                <a href="' . $url . '?status=Open&scope=all" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Open</span>
                    <strong style="font-size: 0.875rem;">' . $gOpen . '</strong>
                </a>
                <a href="' . $url . '?status=Assigned&scope=all" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Assigned</span>
                    <strong style="font-size: 0.875rem;">' . $gAssigned . '</strong>
                </a>
                <a href="' . $url . '?status=Pending&scope=all" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Pending</span>
                    <strong style="font-size: 0.875rem;">' . $gPending . '</strong>
                </a>
                <a href="' . $url . '?status=Complaint&scope=all" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Complaint</span>
                    <strong style="font-size: 0.875rem;">' . $gComplaint . '</strong>
                </a>
                <a href="' . $url . '?status=Resolved&scope=all" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Resolved</span>
                    <strong style="font-size: 0.875rem;">' . $gResolvedTotal . '</strong>
                </a>
            </div>
        ');

        $tTotal = ComplaintRegister::whereDate('created_at', today())->count();
        $tOpen = ComplaintRegister::whereDate('created_at', today())->where('status', 'Open')->count();
        $tAssigned = ComplaintRegister::whereDate('created_at', today())->where('status', 'Assigned')->count();
        $tPending = ComplaintRegister::whereDate('created_at', today())->where('status', 'Pending')->count();
        $tComplaint = ComplaintRegister::whereDate('created_at', today())->where('status', 'Complaint')->count();
        $tResolvedTotal = ComplaintRegister::whereDate('created_at', today())->where('status', 'Resolved')->count();

        $todayBadges = new \Illuminate\Support\HtmlString('
            <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; width: 100%;">
                <a href="' . $url . '?status=Open&scope=all" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Open</span>
                    <strong style="font-size: 0.875rem;">' . $tOpen . '</strong>
                </a>
                <a href="' . $url . '?status=Assigned&scope=all" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Assigned</span>
                    <strong style="font-size: 0.875rem;">' . $tAssigned . '</strong>
                </a>
                <a href="' . $url . '?status=Pending&scope=all" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Pending</span>
                    <strong style="font-size: 0.875rem;">' . $tPending . '</strong>
                </a>
                <a href="' . $url . '?status=Complaint&scope=all" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Complaint</span>
                    <strong style="font-size: 0.875rem;">' . $tComplaint . '</strong>
                </a>
                <a href="' . $url . '?status=Resolved&scope=all" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                    <span style="font-weight: 500; font-size: 0.75rem;">Resolved</span>
                    <strong style="font-size: 0.875rem;">' . $tResolvedTotal . '</strong>
                </a>
            </div>
            <div style="margin-top: 12px;">
                <a href="' . $url . '" style="color: blue; text-decoration: underline; font-size: 0.875rem;">Go to Live Board</a>
            </div>
        ');

        $stats = [
            Stat::make('Total TicketCount', $gTotal)
                ->description($globalBadges)
                ->color('primary'),
                
            Stat::make('Today\'s Ticket Counts', $tTotal)
                ->description($todayBadges)
                ->color('warning'),
                
            Stat::make('Resolved Today', $gResolved)
                ->description('Tickets resolved in the last 24 hours')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Vendor Complaints', \App\Models\VendorComplaint::count())
                ->description(new \Illuminate\Support\HtmlString(
                    '<span style="color: #ef4444;">Unattended: ' . \App\Models\VendorComplaint::where('status', 'Unattended')->count() . '</span> | ' .
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
                    <a href="' . $url . '?status=Assigned" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Assigned</span>
                        <strong style="font-size: 0.875rem;">' . $mAssigned . '</strong>
                    </a>
                    <a href="' . $url . '?status=Pending" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Pending</span>
                        <strong style="font-size: 0.875rem;">' . $mPending . '</strong>
                    </a>
                    <a href="' . $url . '?status=Complaint" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Complaint</span>
                        <strong style="font-size: 0.875rem;">' . $mComplaint . '</strong>
                    </a>
                    <a href="' . $url . '?status=Resolved" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 2px 8px; border-radius: 6px; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                        <span style="font-weight: 500; font-size: 0.75rem;">Resolved</span>
                        <strong style="font-size: 0.875rem;">' . $mResolved . '</strong>
                    </a>
                </div>
            ');

            array_unshift($stats, Stat::make('My Tickets', $mTotal)
                ->description($myBadges)
                ->color('info'));
        }

        return $stats;
    }
}
