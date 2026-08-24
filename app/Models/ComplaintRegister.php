<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComplaintRegister extends Model
{
    protected $table = 'complaint_tickets';

    protected $fillable = [
        'ticket_no',
        'employee_id',
        'section',
        'office_location_id',
        'floor',
        'room_id',
        'complaint_type',
        'description',
        'status',
        'technician_id',
        'remarks',
        'vendor_complaint_id',
        'user_id',
        'created_at',
        'custom_status_date',
    ];

    public $custom_status_date_temp = null;

    public function setCustomStatusDateAttribute($value)
    {
        $this->custom_status_date_temp = $value;
    }

    public function getCustomStatusDateAttribute()
    {
        return $this->custom_status_date_temp;
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function raisedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(OfficeLocation::class, 'office_location_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ComplaintRegisterStatusHistory::class, 'helpdesk_ticket_id')->latest();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_no)) {
                $year = now()->format('Y');
                $cacheKey = 'ticket_seq_' . $year;
                
                // Atomically increment the sequence. If it doesn't exist, we must initialize it.
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    $lock = \Illuminate\Support\Facades\Cache::lock('init_ticket_seq_' . $year, 10);
                    try {
                        $lock->block(5);
                        if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                            $latestTicket = static::whereYear('created_at', now()->year)
                                ->latest('id')
                                ->first();
                            
                            $startCount = 0;
                            if ($latestTicket && preg_match('/IT-\d{4}(\d+)/', $latestTicket->ticket_no, $matches)) {
                                $startCount = intval($matches[1]);
                            } else {
                                $startCount = static::whereYear('created_at', now()->year)->count();
                            }
                            
                            // Initialize cache with the current max
                            \Illuminate\Support\Facades\Cache::forever($cacheKey, $startCount);
                        }
                    } finally {
                        $lock?->release();
                    }
                }

                // Atomic increment prevents any duplicate generation across concurrent requests
                $count = \Illuminate\Support\Facades\Cache::increment($cacheKey);
                $ticket->ticket_no = 'IT-' . $year . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function ($ticket) {
            if ($ticket->isDirty('technician_id') && !$ticket->isDirty('status')) {
                if (empty($ticket->technician_id) && $ticket->getOriginal('status') === 'Assigned') {
                    $ticket->status = 'Open';
                } elseif (!empty($ticket->technician_id) && $ticket->getOriginal('status') === 'Open') {
                    $ticket->status = 'Assigned';
                }
            }
        });

        static::saved(function ($ticket) {
            $isNew = $ticket->wasRecentlyCreated;
            $statusChanged = $ticket->wasChanged('status');
            $remarksChanged = $ticket->wasChanged('remarks');
            $techChanged = $ticket->wasChanged('technician_id');

            if ($isNew || $statusChanged || $remarksChanged || $techChanged) {
                $newStatus = $ticket->status ?? 'Open';
                $newRemarks = $ticket->remarks ?? ($isNew ? 'Ticket created' : 'Status/Remarks updated');
                
                // Prevent duplicate status history if Filament calls save() multiple times
                $lastHistory = $ticket->statusHistories()->latest()->first();
                if ($lastHistory && 
                    $lastHistory->status === $newStatus && 
                    $lastHistory->remarks === $newRemarks && 
                    $lastHistory->technician_id === $ticket->technician_id
                ) {
                    return;
                }

                $historyData = [
                    'status' => $newStatus,
                    'remarks' => $newRemarks,
                    'technician_id' => $ticket->technician_id,
                ];

                if (!empty($ticket->custom_status_date_temp)) {
                    $historyData['created_at'] = $ticket->custom_status_date_temp;
                }

                $ticket->statusHistories()->create($historyData);
            }
        });
    }
}