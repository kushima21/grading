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
            <button type="button" onclick="openCourseModal()">Create New Course</button>
        </div>
    </div>

    <div class="course-table-container">
        <table class="course-table">
            <thead>
                <tr>
                    <th>Course No</th>
                    <th>Descriptive Title</th>
                    <th>Course Components</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($courses as $course)
                {{ $course->course_id }}
    <tr>
        <td>{{ $course->course_no }}</td>
        <td>{{ $course->descriptive_title }}</td>
        <td>{{ $course->course_components }}</td>
        <td>
            <!-- Edit Button -->
            <a href="{{ route('course.edit', $course->id) }}" class="btn btn-edit">Edit</a>

            <!-- Delete Button -->
            <form action="{{ route('course.destroy', $course->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
            </form>
        </td>
    </tr>
@endforeach

            </tbody>
        </table>
    </div>

    <div class="course-modal-container" id="courseModal">
        <h2 class="course-title">Create New Course</h2>
        <div class="course-form">
            <form action="{{ route('course.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="hidden" name="id" id="course_id">
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
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Create Course</button>
                    <button type="button" class="btn btn-secondary" onclick="closeCourseModal()">Cancel</button>
                </div>
            </form>
    </div>

<script>
function openCourseModal() {
    document.getElementById("courseModal").style.display = "block";
}

function closeCourseModal() {
    document.getElementById("courseModal").style.display = "none";

    // Reset form for create mode
    document.getElementById("course_id").value = "";
    document.getElementById("course_no").value = "";
    document.getElementById("descriptive_title").value = "";
    document.getElementById("course_components").value = "";

    // Reset action
    document.querySelector("#courseModal form").action = "{{ route('course.store') }}";
    document.querySelector("#courseModal form").method = "POST";
    document.querySelector("#courseModal form").innerHTML += `@method('POST')`; // fallback
}

// Function to set modal to edit mode
function editCourse(id, course_no, title, components) {
    openCourseModal();

    document.getElementById("course_id").value = id;
    document.getElementById("course_no").value = course_no;
    document.getElementById("descriptive_title").value = title;
    document.getElementById("course_components").value = components;

    let form = document.querySelector("#courseModal form");

    form.action = `/admin/course/${id}`;
    form.method = "POST";

    // Add PUT method field if not existing
    let existingMethod = form.querySelector('input[name="_method"]');
    if (!existingMethod) {
        let methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
    } else {
        existingMethod.value = 'PUT';
    }
}

function editCourse(course) {
    // Change form action and method to PUT
    const form = document.getElementById('courseForm');
    form.action = '/course/' + course.id; // uses update route
    document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    // Set input values
    document.getElementById('course_id').value = course.id;
    document.getElementById('course_no').value = course.course_no;
    document.getElementById('descriptive_title').value = course.descriptive_title;
    document.getElementById('course_components').value = course.course_components;

    // Change button text
    document.getElementById('formSubmitBtn').textContent = 'Update Course';

    // Open modal (assumes you have a function for this)
    openCourseModal();
}

function createNewCourse() {
    // Reset to POST and clear values
    const form = document.getElementById('courseForm');
    form.action = '{{ route("course.store") }}';
    document.getElementById('methodField').innerHTML = '';

    document.getElementById('course_id').value = '';
    document.getElementById('course_no').value = '';
    document.getElementById('descriptive_title').value = '';
    document.getElementById('course_components').value = '';

    document.getElementById('formSubmitBtn').textContent = 'Create Course';

    openCourseModal();
}
</script>

@endsection
