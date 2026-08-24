<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintRegisterStatusHistory extends Model
{
    protected $table = 'complaint_status_histories';

    protected $fillable = [
        'helpdesk_ticket_id',
        'status',
        'remarks',
        'technician_id',
        'created_at',
    ];

    public function ticket()
    {
        return $this->belongsTo(ComplaintRegister::class, 'helpdesk_ticket_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
