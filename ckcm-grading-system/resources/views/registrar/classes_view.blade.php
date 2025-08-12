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
                            <div class="csv-container">
                                <form method="POST" action="">
                                    @csrf
                                    <input type="file" id="students_csv" name="students_csv" accept=".csv" required>
                                    <button type="submit" class="save-btn">
                                        <i class="fa-solid fa-file-arrow-up"></i>
                                        Add Multiple Students
                                    </button> 
                                </form>
                                <p class="csv-p">or add student individually</p>
                            </div>

                            <div class="add-student-indi">
                                <form method="POST" action="">
                                    @csrf
                                    <div class="info-add">
                                        <label for="studentSearch">Find Student</label>
                                        <input type="text" id="studentSearch" class="form-control" placeholder="Search for a student...">
                                        <div class="studentDropDown" class="dropdown-menu"></div>
                                    </div>
                                    <div class="info-add">
                                        <label for="student_id">Student ID</label>
                                        <input type="text" id="student_id" name="student_id" class="form-control" required readonly>
                                    </div>
                                    <div class="info-add">
                                        <label for="name">Name</label>
                                        <input type="text" id="name" name="name" class="form-control" required readonly>
                                    </div>
                                    <div class="info-add">
                                        <label for="gender">Gender</label>
                                        <input type="text" id="gender" name="gender" class="form-control" required readonly>
                                    </div>
                                    <div class="info-add">
                                        <label for="email">Email</label>
                                        <input type="text" id="email" name="email" class="form-control" required readonly>
                                    </div>
                                    <div class="info-add">
                                        <label for="department">Department</label>
                                        <input type="text" id="department" name="department" class="form-control" required readonly>
                                    </div>
                                    <div class="add-studentBtn">
                                        <button type="submit" name="submit">
                                            <i class="fa-solid fa-file-arrow-up"></i>
                                            Add Student
                                        </button>
                                        <button type="button">Cancel</button>
                                    </div>
                                </form>
                            </div>
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
                            <h2 class="grades-score-h">Grades & Scores</h2>
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
