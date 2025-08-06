<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notif_table';

    protected $fillable = [
        'notif_status',
    ];
}
