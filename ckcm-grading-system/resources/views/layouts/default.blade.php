<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/default.css', 'resources/js/app.js'])
    <link rel="icon" href="http://www.yourwebsite.com/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="default-container">

        <nav class="nav-links">

            <div class="main-nav-contents">

                <div class="nav-header">
                    <img src="{{ asset('system_images/icon.png')}}">
                    <label class="gradient-text">
                        CKCM Grading
                    </label>
                    <p>
                         @php
                            $version = 'v0.0.0';
                            $versionFile = base_path('version.txt');
                            if (file_exists(($versionFile))) {
                                $version = trim(string: file_get_contents($version));
                            }
                        @endphp
                        <em>
                            {{$version}}
                        </em>
                    </p>
                </div>

                <div class="nav-profile" id="navProfile">
                    <img src="{{Auth::user()->avatar ?? asset('system_images/user.png')}}" alt="">
                    <div class="profile">
                        <label for="">{{ Auth::user()->name ?? 'Registrar Name' }}<label>
                        <p>ID#: {{ Auth::user()->studentID ?? '000000' }}</p>
                    </div>
                </div>  
                <div class="logout" id="logoutMenu">
                    <div class="settings">
                        <div class="email">
                            <p>Signed in as</p>
                            <p>{{ Auth::user()->email ?? '@email' }}</p>
                        </div>

                        <div class="account">
                            <a href="#">Settings</a>
                        </div>
                        <div class="account">
                            <form method="POST" action="{{('logout')}}">
                                @csrf
                                <button type="submit" class="logout-btn">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-links">
                    <label for="">DASHBOARD</label>
                    <a href="{{ route('index') }}" class="{{ Request::is('/') ? 'active' : '' }}">
                        <span>Home</span>
                    </a>
                    @if (Auth::check() && str_contains(Auth::user()->role, 'student') || str_contains(Auth::user()->role, 'admin'))
                    <a href="{{ route('my_grades') }}" class="{{ Request::is('my_grades') ? 'active' : '' }}" >
                        <span>My Grades</span>
                    </a>
                    @endif

                    <label for="">MANAGE</label>

                     {{-- If the role is "instructor" --}}
                    @if (Auth::check() && str_contains(Auth::user()->role, 'instructor') || str_contains(Auth::user()->role, 'admin'))
                    <a href="{{ route('instructor.my_class') }}" class="{{ Request::is('my_class') ? 'active' : '' }}">
                        <span>My Class</span>
                    </a>
                    @endif
                    {{-- End instructor --}}

                    {{-- If the role is "dean" --}}
                    @if (Auth::check() && str_contains(Auth::user()->role, 'instructor') || str_contains(Auth::user()->role, 'admin'))
                    <a href="{{ route('instructor.my_class_archive') }}"  class="{{ Request::is('my_class_archive') ? 'active' : '' }}">
                        <span>My Class Archive</span>
                    </a>
                     @endif
                    {{-- End dean --}}

                    {{-- If the role is "registrar" OR "dean" --}}
                    @if (Auth::check() && (str_contains(Auth::user()->role, 'dean') || str_contains(Auth::user()->role, 'registrar') || str_contains(Auth::user()->role, 'admin')))
                    <a href="{{ route('show.grades') }}" class="{{ Request::is('allgrades') ? 'active' : '' }}">
                        <span>All Classes</span>
                    </a>
                     @endif
                    {{-- End dean and registrar --}}

                     <a href="#">
                        <span>Student List</span>
                    </a>

                     <a href="#">
                        <span>Course</span>
                    </a>

                    {{-- If the role is "registrar" OR "dean" --}}
                    <a href="#">
                        <span>All Grades</span>
                    </a>

                    <a href="#">
                        <span>Users</span>
                    </a>

                    <label for="">SETTINGS</label>

                     <a href="#">
                        <span>Admin</span>
                    </a>

                    <a href="{{ url('/course') }}">
                        <span>Course</span>
                    </a>
                </div>

                <div class="footer">
                    <h4 class="footer-title">POWERED BY CKCM TECH</h4>
                    <p>&copy; {{date ('Y')}} CKCM Technologies, LLC</p>
                    <p>All Rights Reserved</p>
                </div>

            </div>

        </nav>

        <div class="main-content">
            <div class="content-header">
                <i class="fas fa-bars"></i>
                <i class="fas fa-bell"></i>
            </div>
            @yield('header')
            @yield('content')
        </div>

    </div>
    <script>
    document.getElementById('navProfile').addEventListener('click', function () {
        const logoutMenu = document.getElementById('logoutMenu');
        logoutMenu.style.display = (logoutMenu.style.display === 'block') ? 'none' : 'block';
    });

    // Optional: Hide when clicking outside
    document.addEventListener('click', function(event) {
        const profile = document.getElementById('navProfile');
        const logoutMenu = document.getElementById('logoutMenu');
        if (!profile.contains(event.target) && !logoutMenu.contains(event.target)) {
            logoutMenu.style.display = 'none';
        }
    });
    </script>

</body>
</html>