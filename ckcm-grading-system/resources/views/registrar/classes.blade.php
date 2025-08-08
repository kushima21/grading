@extends('layouts.default')
@vite(['resources/css/classes.css', 'resources/js/app.js'])


@section('content')
 
    <div class="classes-container">
        <div class="classes-header">
            <h2>Classes</h2>
            <form method="" action="" class="my-class-search-form">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search classes..." value="">
            </form>
            <div class="classes-list-header">
                <h2 class="class-l">Class List</h2>
                <button class="addClass" type="button" onclick="openClassModal()">+ Add Classes</button>
            </div>
        </div>
        <div class="classes-class-container">
            <table class="table-class-list">
                <thead>
                    <tr>
                        <th>Course No</th>
                        <th>Descriptive Title</th>  
                        <th>Units</th>
                        <th>Instructor</th>
                        <th>Academic Period</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CS101</td>
                        <td>Introduction to Computer Science</td>
                        <td>3</td>
                        <td>Hondrada John Mark</td>
                        <td>1st Semester</td>
                        <td>Mon 7:30am - 10:00am</td>
                        <td>Active</td>
                        <td>

                            <a href="#">
                                <i class="fa-solid fa-up-right-from-square"></i>
                                View Class |
                            </a>
                             <a href="#">
                                <i class="fa-solid fa-pen"></i>
                                Edit 
                            </a>
                            <a href="#">
                                |
                                <i class="fa-solid fa-trash"></i>
                                delete
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="class-modal" id="classModal">
        <div class="modal-container">
            <h2 class="class-modal-header">
                Create New Class
            </h2>
            <form method="POST" action="{{('classes.create')}}">
                @csrf
                @method("POST")
                <div class="info-container">
                    <label for="courseSearch">Course No: <em>*Example: GEC 001*</em></label>
                    <input type="text" id="courseSearch" name="course_no" class="form-control"
                    placeholder="Search for Course No..." value="{{ old('course_no') }}" oninput="filterCourses()" autocomplete="off" required>
                    <div id="courseDropdown" class="dropdown-menu"></div>
                </div>

                <div class="info-container">
                    <label>Descriptive Title:</label>
                    <input type="hidden" id="descriptive_title" name="descriptive_title" value="" readonly>
                    <p id="descriptive_title_text">
                </div>

                <div class="info-container">
                    <label for="units">Units:</label>
                    <select id="units" name="units">
                        <option value="" disabled {{ old('units', isset($course) ? $course->units : '') == '' ? 'selected' : '' }}>Select Units</option>
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('units', isset($course) ? $course->units : '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="info-container">
                    <label for="instructorSearch">Instructor</label>
                    <input type="text" id="instructorSearch" name="instructor" class="form-control" 
                    placeholder="Search for a instructor...">
                    <div id="instructorDropdown" class="dropdown-menu">
                </div>

                <div class="info-container">
                <label for="academic_year">Academic Year</label>
                <select id="academic_year" name="academic_year">
                    <option value="" disabled {{ old('academic_year') ? '' : 'selected' }}>Select Academic Year</option>
                    @for ($year = 2024; $year <= date('Y') + 5; $year++)
                        <option value="{{ $year }}-{{ $year + 1 }}" {{ old('academic_year') == "$year-$year+1" ? 'selected' : '' }}>
                            {{ $year }}-{{ $year + 1 }}
                        </option>
                    @endfor
                </select>
                </div>

                <div class="info-container">
                <label for="academic_period">Academic Period</label>
                    <select id="academic_period" name="academic_period">
                    <option value="" disabled {{ old('academic_period') ? '' : 'selected' }}>Select Academic Period</option>
                    <option value="1st Semester" {{ old('academic_period') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd Semester" {{ old('academic_period') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    <option value="Summer" {{ old('academic_period') == 'Summer' ? 'selected' : '' }}>Summer</option>
                </select>
                </div>

                 <div class="info-container">
                    <label for="schedule" >Schedule: <em>*Example: Monday, Tuesday, Wednesday*</em></label>
                    <input type="text" id="schedule" name="schedule" value="{{ old('schedule') }}" required>
                </div>

                 <div class="info-container">
                    <label for="status" >Status: <em>*automatically active upon adding*</em></label>
                    <input type="status" id="status" name="status" value="Active" readonly>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="save-btn"><i class="fa-solid fa-file-arrow-up"></i> Add Class</button>
                    <button type="button" class="close-btn" onclick="closeClassModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script>
function openClassModal() {
    document.getElementById("classModal").classList.add("show");
}

function closeClassModal() {
    document.getElementById("classModal").classList.remove("show");
}
</script>
<script>
    function filterInstructors() {
        let input = document.getElementById("instructorSearch").value.toLowerCase();
        let dropdown = document.getElementById("instructorDropdown");
        dropdown.innerHTML = ""; // Clear previous results

        if (input.trim() === "") {
            dropdown.style.display = "none";
            return;
        }

        let instructors = {!! json_encode($instructors) !!}; // Use Blade variable
        let filtered = instructors.filter(instructor =>
            instructor.name.toLowerCase().includes(input)
        );

        if (filtered.length === 0) {
            dropdown.style.display = "none";
            return;
        }

        filtered.forEach(instructor => {
            let option = document.createElement("div");
            option.classList.add("dropdown-item");
            option.textContent = instructor.name;
            option.onclick = function() {
                document.getElementById("instructorSearch").value = instructor.name;
                dropdown.style.display = "none";
            };
            dropdown.appendChild(option);
        });

        dropdown.style.display = "block";
    }
</script>
@endsection