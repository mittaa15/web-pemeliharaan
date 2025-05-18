<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairHistory extends Model
{
    protected $table = 'repair_history';
    protected $fillable = [
        'id_report',
        'status',
        'complete_date',
        'repair_notes',
        'damage_photo'
    ];
}
