<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $table = 'building';
    protected $fillable = [
        'building_name',
        'description'
    ];

    public function building()
    {

        return $this->belongsTo(Building::class, 'id_building');
    }

    public function buildingRoom()
    {
        return $this->belongsTo(Building::class);
    }
}