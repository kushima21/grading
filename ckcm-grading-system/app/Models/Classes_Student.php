<?php

namespace App\Models;

use App\Http\Controllers\RegistrarController;
use Illuminate\Database\Eloquent\Model;

class Classes_Student extends Model
{
    protected $table = 'classes_student';

    protected $fillable = [
        'classID',
        'studentID',
        'name',
        'gender',
        'email',
        'deparment',
    ];
}
