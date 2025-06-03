<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RepairHistory extends Model
{
    use HasFactory;

    protected $table = 'repair_history';
    protected $fillable = [
        'id_report',
        'status',
        'complete_date',
        'repair_notes',
        'damage_photo'
    ];
}