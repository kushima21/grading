@extends('layouts.default')
@vite(['resources/css/my_class_archive.css', 'resources/js/app.js'])

@section('content')
    <div class="my-archive-container">

        <div class="my-archive-header">

            <h2 class="class-archive-heading">
                My Class Archived
            </h2>

            <form method="POST" action="" class="filter-form">

                <select name="academic_year">
                    <option value="">Select Academic Year</option>
                </select>

                <input type="text" name="course_no" placeholder="Search Course No...">
                <button type="submit">Filter</button> 

            </form>

        </div>

       <div class="class-archive-container">
            <div class="folder" onclick="toggleFolder('academicPeriod')">
                📁 Academic Year:
            </div>
                <div class="folder-content" id="academicPeriod" style="display: none;" onclick="toggleFolder('courseNo', event)">
                    📂 Academic Period:
                </div>
                    <div class="course-content" id="courseNo" style="display: none;" onclick="showResultContent(event)">
                        📂 Course No:
                    </div>

                    <div class="result-content-container">
                        <div class="result-content" id="resultContent">
                            <h4 class="instructor-header">Instructor: </h4>
                            <h5 class="decriptive-title-header">Descriptive Title:</h5>
                            <h5 class="schedule-header">Schedule:</h5>
                        </div>
                    </div>
        </div>

    </div>
@endsection
<script>
function toggleFolder(id, event) {
    if (event) {
        event.stopPropagation(); // para dili mo-trigger ang parent click
    }
    const folder = document.getElementById(id);
    folder.style.display = (folder.style.display === "none") ? "block" : "none";
}

function showResultContent(event) {
    event.stopPropagation();
    const result = document.getElementById('resultContent');
    result.style.display = (result.style.display === "none") ? "block" : "none";
}
</script>