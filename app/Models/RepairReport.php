<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RepairReport extends Model
{
    use HasFactory;

    protected $table = 'repair_report';
    protected $fillable = [
        'id_user',
        'id_building',
        'id_room',
        'id_facility_building',
        'id_facility_room',
        'id_technicians',
        'damage_description',
        'damage_photo',
        'status',
        'location_type',
        'damage_impact',
        'damage_point'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

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

    public function latestHistory()
    {
        return $this->hasOne(RepairHistory::class, 'id_report', 'id')->latestOfMany();
    }


    public function schedules()
    {
        return $this->hasOne(RepairSchedule::class, 'id_report');
    }

    public function repairTechnicians()
    {
        return $this->hasMany(RepairTechnicians::class, 'id_report', 'id');
    }

    public function getLaporanByFacility($id)
    {
        $laporan = RepairReport::with(['user', 'buildingFacility', 'roomFacility', 'histories', 'schedules'])
            ->where('id_facility_building', $id)
            ->orWhere('id_facility_room', $id)
            ->get();

        return response()->json($laporan);
    }

    public function technician()
    {
        // Contoh: relasi belongsTo ke model User, dengan foreign key id_technician
        return $this->belongsTo(User::class, 'id_technician');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'id_report');
    }
}