<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'id_report',
        'id_user',
        'complaint_description',
        'complaint_status',
        'complaint_date'
    ];
}