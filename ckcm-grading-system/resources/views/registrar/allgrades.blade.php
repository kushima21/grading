@extends('layouts.default')
@vite(['resources/css/allgrades.css', 'resources/js/app.js'])


    @section('content')
        <div class="all-class-header">

            <h2 class="all-grades">
                All Grades
            </h2>

            <div class="filtering-grades">
                <form method="POST">
                    <i class="fas fa-search"></i>
                    <input type="text" name="course" id="course" class="form-control" placeholder="Quick Search...">
                    
                    <select id="departmentFilter" class="form-control">
                        <option value="">Select Department</option>
                        <option value="">Bachelor Science In Computer Science</option>
                        <option value="">Bachelor of Secondary Education</option>
                    </select>

                    <select id="academicPeriodFilter" class="form-control">
                        <option value="">Select Academic Period</option>
                        <option value="">1st Semester</option>
                        <option value="">2nd Semester</option>
                        <option value="">Summer</option>
                    </select>

                    <select id="academicYearFilter" class="form-control">
                        <option value="">Select Academic Year</option>
                        <option value="">2025-2026</option>
                        <option value="">2026-2027</option>
                    </select>

                    <div class="course-info-container">
                        <h3>CS-10</h3>
                        <h2 class="descriptive-header">Programming 1</h2>
                        <span>- Marjon D. Senarlo</span>
                    </div>
                </form>
            </div>

          <div class="student-all-grades-container">
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Academic Period</th>
                        <th>Academic Year</th>
                        <th>Prelim</th>
                        <th>MidTerm</th>
                        <th>Semi Finals</th>
                        <th>Final</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hondrada John Mark</td>
                        <td>johhondrada@ckcm.edu.ph</td>
                        <td>Male</td>
                        <td>Bachelor of Science in Computer Science</td>
                        <td>1st Semester</td>
                        <td>2025-2026</td>
                        <td>1.90</td>
                        <td>2.0</td>
                        <td>3.0</td>
                        <td>2.7</td>
                        <td>Passed</td>
                    </tr>
                </tbody>
            </table>
        </div>


        </div>
    @endsection