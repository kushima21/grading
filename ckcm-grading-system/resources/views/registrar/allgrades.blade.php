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

                    <h2 class="descriptive-header">Programming 1</h2>
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
                        <th>Action</th>
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
                        <td>
                            <a href="#">
                                <i class="fa-solid fa-up-right-from-square"></i> 
                                view class
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        </div>
    @endsection