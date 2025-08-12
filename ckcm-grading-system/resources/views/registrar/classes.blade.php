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
            @forelse ($classes as $class)
                <tr>
                    <td>{{ $class->course_no }}</td>
                    <td>{{ $class->descriptive_title }}</td>
                    <td>{{ $class->units }}</td>
                    <td>{{ $class->instructor }}</td>
                    <td>{{ $class->academic_period }} {{ $class->academic_year }}</td>
                    <td>{{ $class->schedule }}</td>
                    <td>{{ $class->status }}</td>
                    <td>
                        <a href="{{ route('class.show', $class->id) }}">
                            <i class="fa-solid fa-up-right-from-square"></i> View Class |
                        </a>

                        <a href="#">
                            <i class="fa-solid fa-pen"></i> Edit 
                        </a>
                        <a href="#">
                            | <i class="fa-solid fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No classes added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

        <div class="class-modal" id="classModal">
        <div class="modal-container">
            <h2 class="class-modal-header">
                Create New Class
            </h2>
            <form method="POST" action="{{ route('classes.create') }}">
                @csrf
                @method("POST")
                <div class="info-container" style="position: relative;">
                    <label for="courseSearch">Course No: <em>*Example: GEC 001*</em></label>
                    <input type="text" id="courseSearch" name="course_no" class="form-control"
                        placeholder="Search for Course No..." value="{{ old('course_no') }}"
                        oninput="filterCourses()" autocomplete="off" required>
                    <div id="courseDropdown" class="dropdown-menu"></div>
                </div>

                <div class="info-container">
                    <label>Descriptive Title:</label>
                    <input type="hidden" id="descriptive_title" name="descriptive_title" value="" readonly>
                    <p id="descriptive_title_text"></p>
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

                <div class="info-container" style="position: relative;">
                    <label for="instructorSearch">Instructor</label>
                    <input type="text" id="instructorSearch" name="instructor" class="form-control"
                        placeholder="Search for an instructor..." oninput="filterInstructors()" autocomplete="off">
                    <div id="instructorDropdown" class="dropdown-menu"></div>
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

                <input type="hidden" name="added_by" value="{{ Auth::user()->name ?? 'Test User' }}">



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
function filterCourses() {
    const courseInput = document.getElementById('courseSearch');
    const descriptiveTitleInput = document.getElementById('descriptive_title');
    const descriptiveTitleText = document.getElementById('descriptive_title_text');
    const dropdown = document.getElementById('courseDropdown');
    const query = courseInput.value.trim();

    dropdown.innerHTML = '';

    if (query.length < 1) {
        dropdown.style.display = 'none';
        descriptiveTitleInput.value = '';
        descriptiveTitleText.textContent = '';
        return;
    }

    fetch(`/course-search?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            dropdown.innerHTML = '';
            if (data.length > 0) {
                data.forEach(course => {
                    const item = document.createElement('div');
                    item.classList.add('dropdown-item');
                    item.textContent = course.course_no;
                    item.style.cursor = 'pointer';

                    item.addEventListener('click', () => {
                        courseInput.value = course.course_no;
                        descriptiveTitleInput.value = course.descriptive_title;
                        descriptiveTitleText.textContent = course.descriptive_title;
                        dropdown.style.display = 'none';
                    });

                    dropdown.appendChild(item);
                });
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
                descriptiveTitleInput.value = '';
                descriptiveTitleText.textContent = '';
            }
        })
        .catch(() => {
            dropdown.style.display = 'none';
            descriptiveTitleInput.value = '';
            descriptiveTitleText.textContent = '';
        });
}

// Hide dropdown when clicking outside
document.addEventListener('click', function (e) {
    const input = document.getElementById('courseSearch');
    const dropdown = document.getElementById('courseDropdown');
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
<script>
function filterInstructors() {
    const input = document.getElementById('instructorSearch');
    const dropdown = document.getElementById('instructorDropdown');
    const query = input.value.trim();

    dropdown.innerHTML = '';

    if (query.length < 1) {
        dropdown.style.display = 'none';
        return;
    }

    fetch(`/instructor-search?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                data.forEach(instructor => {
                    const item = document.createElement('div');
                    item.classList.add('dropdown-item');
                    item.style.cursor = 'pointer';
                    item.textContent = instructor.name;
                    item.addEventListener('click', () => {
                        input.value = instructor.name;
                        dropdown.style.display = 'none';
                    });
                    dropdown.appendChild(item);
                });
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        })
        .catch(() => {
            dropdown.style.display = 'none';
        });
}

// Hide dropdown if clicked outside
document.addEventListener('click', function (e) {
    const input = document.getElementById('instructorSearch');
    const dropdown = document.getElementById('instructorDropdown');
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>


@endsection