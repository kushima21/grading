@extends('layouts.default')
@section('content')
    <div class="dashboard">
        @if (Auth::check())
            <h1>Welcome, {{ Auth::user()->name }}!</h1>
            <h2>Your Role is, {{ Auth::user()->role }}!</h2>
            @if (Auth::check() && str_contains(Auth::user()->role, 'student'))
                <a href="{{ route('my_grades') }}"><i class="fa-solid fa-arrow-up-right-from-square"></i> Go to My Grades</a>
            @endif
        @endif
    </div>

    @if($needsProfileUpdate)
        <style>
            #profileUpdateModal {
                display: block;
                position: fixed;
                z-index: 999;
                padding-top: 150px;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modal-content {
                height: fit-content;
                border: 1px solid var(--color5);
                background-color: var(--ckcm-color1);
                margin: 2% auto;
                padding: 20px;
                border-radius: 10px;
                width: 30%;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            }

            .modal-content h2 {
                color: var(--ckcm-color4);
            }
        </style>

        <div id="profileUpdateModal">
            <div class="modal-content">
                <h2 style="margin-bottom: 10px;">Complete Your Student Profile</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('POST')

                    <div class="mb-3">
                        <label for="studentID">ID</label>
                        <input type="text" id="studentID" name="studentID"
                            value="{{ old('studentID', Auth::user()->studentID) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="" disabled selected>Select your department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->department_name }}" {{ Auth::user()->department == $dept->department_name ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>Select your gender</option>
                            <option value="Male" {{ Auth::user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ Auth::user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>

                    </div>


                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="text" id="password" name="password" required>
                        <em style="color:var(--ckcm-color4); text-align: left; font-size: 1.2rem; margin-top: 5px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Please ensure the accuracy of your information, as it will directly impact your account details.
                        </em>
                    </div>


                    <style>
                        .mb-3 {
                            display: flex;
                            flex-direction: column;
                            gap: 5px;
                        }

                        .mb-3 label {
                            margin-top: 5px;
                            font-size: 1.2rem;
                            color: var(--color2);
                        }
                    </style>

                    <button class="save-btn" type="submit">Save Changes</button>
                </form>
            </div>
        </div>
    @endif
@endsection

<style>
    .dashboard {
        margin-top: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 10px;
    }

    a {
        font-size: 1.2rem;
        color: var(--ckcm-color4);
        text-decoration: none;
    }

    a:hover {
        color: var(--ckcm-color3);
    }

    .dashboard h1 {
        color: var(--ckcm-color4);
    }

    .dashboard h2 {
        color: var(--color6);
    }

    @media (max-width: 480px) {
        #profileUpdateModal .modal-content {
            height: fit-content;
            border: 1px solid var(--color5);
            background-color: var(--ckcm-color1);
            margin: 2% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard h1 {
            color: var(--ckcm-color4);
        }

        .dashboard h2 {
            color: var(--color6);
        }
    }

    @media (max-width: 768px) {
        .dashboard {
            margin-top: 200px;
        }

        .dashboard h1 {
            color: var(--ckcm-color4);
            font-size: 1.5rem;
        }

        .dashboard h2 {
            color: var(--color6);
            font-size: 1.2rem;
        }
    }
</style>
