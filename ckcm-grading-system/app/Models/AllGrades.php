<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllGrades extends Model
{
    protected $table = 'grade_logs'; // Ensures the correct table is used

    protected $fillable = [
        'id',
        'studentID',
        'subject_code',
        'descriptive_title',
        'units',
        'prelim',
        'midterm',
        'semi_finals',
        'final',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'studentID', 'studentID');
    }
}
