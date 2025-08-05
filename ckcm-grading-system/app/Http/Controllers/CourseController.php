<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
public function index()
{
    $courses = Course::select('id', 'course_no', 'descriptive_title', 'course_components')->get();
    return view('admin.course', compact('courses'));
}

    public function store(Request $request)
    {
        $request->validate([
            'course_no' => 'required',
            'descriptive_title' => 'required',
            'course_components' => 'required',
        ]);

        Course::create($request->all());

        return redirect()->back()->with('success', 'Course added successfully!');
    }

    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Course deleted successfully!');
    }

    public function edit($id)
{
    $course = Course::findOrFail($id);
    return view('admin.edit-course', compact('course'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'course_no' => 'required',
        'descriptive_title' => 'required',
        'course_components' => 'required',
    ]);

    $course = Course::findOrFail($id);
    $course->update($request->all());

    return redirect()->route('admin.index')->with('success', 'Course updated successfully!');
}

}

