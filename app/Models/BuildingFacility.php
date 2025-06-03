<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class BuildingFacility extends Model
{
    use HasFactory;
    protected $table = 'building_facility';
    protected $fillable = [
        'id_building',
        'facility_name',
        'location',
        'description'
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'id_building');
    }
    // BuildingFacility.php
    // Contoh di BuildingFacility.php
    public function repairReports()
    {
        return $this->hasMany(RepairReport::class, 'id_facility_building', 'id');
        // sesuaikan dengan struktur tabel
    }
}
