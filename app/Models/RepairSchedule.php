<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RepairSchedule extends Model
{
    use HasFactory;

    protected $table = 'repair_schedule';
    protected $fillable = [
        'id_report',
        'technician_name',
        'repair_date',
    ];
}