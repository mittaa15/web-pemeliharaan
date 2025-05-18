<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'room';

    protected $fillable = [
        'id_building',
        'room_name',
        'room_type',
        'capacity',
        'description',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class, 'id_building');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}