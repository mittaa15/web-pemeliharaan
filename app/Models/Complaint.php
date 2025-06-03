<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaint';
    protected $fillable = [
        'id_report',
        'id_user',
        'complaint_description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function repairReport()
    {
        return $this->belongsTo(RepairReport::class, 'id_report');
    }
}