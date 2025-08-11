@extends('layouts.default')
@vite(['resources/css/studentList.css', 'resources/js/app.js'])


      @section('content')

        <div class="all-student-list-header">

             <h2 class="all-student-header">
                Student List
            </h2>

            <div class="all-student-container">
                <form method="POST">
                    <i class="fas fa-search"></i>
                    <input type="searchStudent" name="name" id="searchStudent" placeholder="Quick Search...">
                </form>
            </div>

        </div>

        <div class="all-student-container-table">
            <table class="all-student-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hondrada John Mark</td>
                        <td>johhondrada@ckcm.edu.ph</td>
                        <td>Male</td>
                        <td>Bachelor of Science in Computer Science</td>
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

      @endsection