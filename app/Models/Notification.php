<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $fillable = [
        'id_user',
        'isRead',
        'title',
        'description',
    ];

    public static function createNotification($id_user, $title, $description, $isRead = false)
    {
        return self::create([
            'id_user'     => $id_user,
            'title'       => $title,
            'description' => $description,
            'isRead'      => $isRead,
        ]);
    }
}