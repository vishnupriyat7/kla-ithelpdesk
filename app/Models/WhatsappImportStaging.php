<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappImportStaging extends Model
{
    protected $table = 'whatsapp_import_stagings';

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
        'remarks',
        'vendor_complaint_id',
        'technician_id',
        'user_id',
        'reported_at',
        'source_key',
        'raw_messages',
        'merge_count',
        'already_imported',
        'imported_ticket_id',
        'imported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'imported_at' => 'datetime',
        'already_imported' => 'boolean',
    ];

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

    public function importedTicket()
    {
        return $this->belongsTo(ComplaintRegister::class, 'imported_ticket_id');
    }
}
