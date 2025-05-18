<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairReport extends Model
{
    protected $table = 'repair_report';
    protected $fillable = [
        'id_user',
        'id_building',
        'id_room',
        'id_facility_building',
        'id_facility_room',
        'damage_description',
        'damage_photo',
        'status',
        'location_type',
        'damage_impact',
        'damage_point'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'id_room');
    }

    public function roomFacility()
    {
        return $this->belongsTo(RoomFacility::class, 'id_facility_room');
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'id_building');
    }

    public function buildingFacility()
    {
        return $this->belongsTo(BuildingFacility::class, 'id_facility_building');
    }

    public function histories()
    {
        return $this->hasMany(RepairHistory::class, 'id_report');
    }
}
