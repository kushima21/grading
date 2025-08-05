<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Optional if your table is not the plural of the model name
    protected $table = 'course';

    // ✅ Add these fields that match your form and DB columns
   protected $fillable = ['course_no', 'descriptive_title', 'course_components'];
}
