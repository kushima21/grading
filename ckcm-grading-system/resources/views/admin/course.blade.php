@extends('layouts.default')
@vite(['resources/css/course.css', 'resources/js/app.js'])

@section('content')
    <div class="course-header-container">
        <div class="course-header">
            <h2>Course Management</h2>
            <form method="GET" action="" class="course-search-form">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search courses..." value="">
            </form>  
        </div>
        <div class="courseBtn">
            <button type="button">Create New Course</button>
        </div>
    </div>
    <div class="course-modal-container">
        <h2 class="course-title">Create New Course</h2>
        <div class="course-form">
            <form action="" method="POST">
                @csrf
                <div class="form-group">
                    <label for="course_no">Course No:</label>
                    <br>
                    <input type="text" name="course_no" id="course_no" placeholder="Course No" required>
                    <br>
                    <label for="descriptive_title">Descriptive Title:</label>
                    <br>
                    <input type="text" name="descriptive_title" id="descriptive_title" placeholder="Descriptive Title" required>
                    <br>
                    <label for="course_components">Course Components:</label>
                    <br>
                    <select name="course_components" id="course_components" required>
                        <option value="" disabled selected>--Select Course Component--</option>
                        <option value="General Education">General Education</option>
                        <option value="Major/Specialization">Major/Specialization</option>
                        <option value="Physical Education">Physical Education</option>
                        <option value="NSTP">NSTP</option>
                        <option value="Religious Studies">Religious Studies</option>
                        <option value="Professional Courses">Professional Courses</option>
                        <option value="Professional Education">Professional Education</option>
                        <option value="CS Electives">CS Electives</option>
                        <option value="BSBA Electives">BSBA Electives</option>
                        <option value="Allied Course">Allied Course</option>
                        <option value="Allied">Allied</option>
                        <option value="EDUC 100">EDUC 100</option>
                        <option value="EdEng">EdEng</option>
                        <option value="EdMath">EdMath</option>
                        <option value="Student Formation Course">Student Formation Course</option>
                        <option value="Business Administration Core Courses">Business Administration Core Courses</option>
                        <option value="Common Business Management Education Courses">Common Business Management Education Courses</option>
                        <option value="Cognates">Cognates</option>
                        <option value="Mandated Courses">Mandated Courses</option>
                        <option value="FLE">FLE</option>
                        <option value="CS">CS</option>
                        <option value="BA">BA</option>
                        <option value="BSBA">BSBA</option>
                        <option value="OJT">OJT</option>
                        <option value="ELS">ELS</option>
                        <option value="GMRC">GMRC</option>
                        <option value="MTB">MTB</option>
                        <option value="Other Courses">Other Courses</option>
                        
                    </select>
                </div>
                
            </form>
    </div>
@endsection
