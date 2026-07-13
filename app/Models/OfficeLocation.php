<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeLocation extends Model
{

    use SoftDeletes;

    protected $fillable = ['location', 'administrative_office_id'];

    protected static function booted()
    {
        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('administrative_office_id')->orderBy('id');
        });
    }

    public function office()
    {
        return $this->belongsTo(AdministrativeOffice::class, 'administrative_office_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

}
