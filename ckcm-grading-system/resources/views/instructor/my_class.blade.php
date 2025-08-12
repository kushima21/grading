@extends('layouts.default')
@vite(['resources/css/my_class.css', 'resources/js/app.js'])

@section('content')
    <div class="my-class-container">
        <div class="my-class-header">
            <h2>My Classes</h2>
            <form method="GET" action="" class="my-class-search-form">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search classes..." value="">
            </form>
        </div>
        <div class="class-list">
            <h2 class="class-list-header">Class List</h2>
            <table class="table-class-list">
                <thead>
                    <tr>
                        <th>Course No</th>
                        <th>Descriptive Title</th>
                        <th>Instructor</th>
                        <th>Academic Period</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classes as $class)
                <tr>
                    <td>{{ $class->id }}</td>
                    <td>{{ $class->subject_code }}</td>
                    <td>{{ $class->descriptive_title }}</td>
                    <td>{{ $class->instructor }}</td>
                    <td>{{ $class->academic_period }}</td>
                    <td>{{ $class->schedule }}</td>

                    <td
                        class="status {{ strtolower($class->status) }}">{{ $class->status }}</td>


                    <td style="text-align:center; background-color: var(--color9b);">
                        <!-- Edit Button -->
                        <a href="{{ route('class.show', $class->id) }}" class="view-btn"><i
                                class="fa-solid fa-up-right-from-square"></i> View Class</a>

                    </td>
                </tr>

            @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection