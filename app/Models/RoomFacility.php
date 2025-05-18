<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomFacility extends Model
{
    protected $table = 'room_facility';

    protected $fillable = [
        'id_room',
        'facility_name',
        'number_units',
        'description',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'id_room');
    }
}