<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Technician extends Model
{
    use HasFactory;

    protected $table = 'technicians';

    protected $fillable = [
        'name',
        'email',
        'phone_number'
    ];


    public function repairReports()
    {
        return $this->belongsToMany(RepairReport::class, 'repair_technicians', 'id_technisian', 'id_report');
    }
}
