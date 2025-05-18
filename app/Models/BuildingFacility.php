<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuildingFacility extends Model
{
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
}