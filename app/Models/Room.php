<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'floor_id',
        'block',
        'office_location_id',
        'is_office',
        'comment'
    ];

    public function location()
    {
        return $this->belongsTo(OfficeLocation::class, 'office_location_id');
    }

    public function floorLevel()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }
}
