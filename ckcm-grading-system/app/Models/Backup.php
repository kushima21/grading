<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Listeners\BackupDatabaseListener;

class Backup extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            (new BackupDatabaseListener())->handle();
        });
    }
}
