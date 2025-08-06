<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedFinalGrade extends Model
{
    protected $table = 'archived_final_grades';

    protected $fillable = [
        'classID', 'subject_code', 'descriptive_title', 'units', 'instructor',
        'academic_period', 'academic_year', 'schedule', 'studentID', 'name',
        'gender', 'email', 'department', 'prelim', 'midterm', 'semi_finals',
        'final', 'remarks', 'status', 'added_by'
    ];

    public $timestamps = true;

    // Optional relationship
    public function class()
    {
        return $this->belongsTo(ClassArchive::class, 'classID', 'id');
    }
}
