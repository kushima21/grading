<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Percentage extends Model
{
    protected $table = 'percentage';

    protected $fillable = [
        'classID',
        'periodic_term',
        'quiz_percentage',
        'quiz_total_score',
        'attendance_percentage',
        'attendance_total_score',
        'assignment_percentage',
        'assignment_total_score',
        'exam_percentage',
        'exam_total_score',
    ];

}
