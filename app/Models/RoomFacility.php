<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomFacility extends Model
{

    use HasFactory;

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

    // RoomFacility.php
    // Contoh di RoomFacility.php
    public function repairReports()
    {
        return $this->hasMany(RepairReport::class, 'id_facility_room', 'id');
        // pastikan foreign key dan local key benar
    }
}
