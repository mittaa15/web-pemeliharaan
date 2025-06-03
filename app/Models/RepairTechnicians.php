<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RepairTechnicians extends Model
{
    use HasFactory;

    protected $table = 'repair_technicians';

    protected $fillable = [
        'id_report',
        'id_technisian',
        'description_work'
    ];

    public function technician()
    {
        return $this->belongsTo(Technician::class, 'id_technisian', 'id');
    }
}