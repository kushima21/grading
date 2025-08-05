@extends('layouts.default')
@vite(['resources/css/classes_view.css', 'resources/js/app.js'])


@section('header')
        <div class="classes-view-header">
            <h2 class="classes-view-title1">Classes Details</h2>
            <h2 class="classes-view-title2">Classes Details</h2>
            <div class="classes-details-container">
                <p><strong>Course No:</strong> CS10</p>
                <p><strong>Instructor:</strong> Hondrada John Mark</p>
                <p><strong>Academic Period:</strong> 1st Semester</p>
                <p><strong>Schedule:</strong> Mon 10:30am - 12:00pm</p>
                <p><strong>Status:</strong> Active</p>
            </div>
            <div class="classes-link-container">
                 <ul>
                    <li><a href="#studentlist">Class Students List</a></li>
                    <li><a href="#gradesscores">Grades & Scores</a></li>
                    <li><a href="#classlist">Grades</a></li>
                </ul>
                <button type="button" class="btn-primary" onclick="openAddStudentModal()">
                    <i class="fa-solid fa-plus"></i> Add Student
                </button>
            </div>
        </div>
@endsection
@section('content')
        <div class="classes-view-container">
            <div class="addStudent-modal" id="addStudentModal">
                <h2 class="add-student-title">Add New Student</h2>
            </div>
            <div class="classes-box-main-container">
                <div class="student-list" id="studentlist">
                    <h2>Class Students List</h2>
                    <form method="GET" action="" class="student-search-form">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search students..." value="">
                    </form>
                </div>
                <div class="student-list" id="gradesscores">
                    <h2>Grades & Scores</h2>
                </div>
                <div class="student-list" id="classlist">
                    <h2>Grades</h2>
                </div>
            </div>
        </div>
@endsection
<script>
    function openAddStudentModal() {
        document.getElementById("addStudentModal").style.display = "block";
    }

    function closeAddStudentModal() {
        document.getElementById("addStudentModal").style.display = "none";
    }

    // Optional: Close when clicking outside modal-content
    window.onclick = function(event) {
        const modal = document.getElementById("addStudentModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>
