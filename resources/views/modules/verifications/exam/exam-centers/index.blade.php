@extends('layouts.auth')

@section('title', 'Exam Center Check In')

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card overflow-hidden">
            <div class="bg-primary-subtle">
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-4">
                            <h5 class="text-primary">Welcome Back !</h5>
                            <p>Enter Registration Number to sign for exam center.</p>
                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0"> 
                <div class="auth-logo">
                    <a href="{{ url('/') }}" class="auth-logo-light">
                        <div class="avatar-md profile-user-wid mb-4">
                            <span class="avatar-title rounded-circle bg-light">
                                <img src="{{ asset('assets/images/logo-light.svg') }}" alt="" class="rounded-circle" height="34">
                            </span>
                        </div>
                    </a>

                    <a href="{{ url('/') }}" class="auth-logo-dark">
                        <div class="avatar-md profile-user-wid mb-4">
                            <span class="avatar-title rounded-circle bg-light">
                                <img src="{{ asset('assets/images/logo.svg') }}" alt="" class="rounded-circle" height="34">
                            </span>
                        </div>
                    </a>
                </div>
                <div class="p-2">
                    @if (!isset($student))
    {{-- Search form --}}
    <form class="form-horizontal" action="{{ route('verify.exam.exam-centers.verify') }}" method="POST">
        @csrf

        {{-- Username input --}}
        <div class="mb-3">
            <label for="username" class="form-label">Registration Number</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror"
                   id="username" name="username" value="{{ old('username') }}" required autofocus>
            @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Global error messages --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error message --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-3 d-grid">
            <button class="btn btn-primary waves-effect waves-light" type="submit">Check In</button>
        </div>
    </form>
@else
    {{-- Student Profile --}}
    <div class="card">
        <div class="card-header bg-light">
            <strong>Student Details</strong>
        </div>
        {{-- Student details --}}

    <div class="table-responsive">
        <table class="table table-nowrap mb-0">
            <tbody>
                <tr>
                    <th scope="row">Full Name :</th>
                    <td>{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</td>
                </tr>
                <tr>
                    <th scope="row">Program :</th>
                    <td>{{ $student->program_name }}</td>
                </tr>
                <tr>
                    <th scope="row">Campus :</th>
                    <td>{{ $student->campus_name }}</td>
                </tr>

                @if (isset($enrollment))
                    <tr>
                        <th scope="row">Enrolled Exam Center :</th>
                        <td>{{ $enrollment->enrolled_campus }}</td>
                    </tr>
                @elseif (isset($availableCenters))
                    <tr>
                        <th scope="row">Exam Center Enrollment :</th>
                        <td>
                            <form action="{{ route('verify.exam.exam-centers.enroll') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $student->user_id }}">
                                <div class="mb-2">
                                    <select name="exam_center_id" class="form-control form-control-sm" required>
                                        <option value="">Choose a center</option>
                                        @foreach ($availableCenters as $center)
                                            <option value="{{ $center->id }}">{{ $center->campus_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-success">Enroll</button>
                            </form>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    </div>
@endif


                </div>
            </div>
        </div>
        <div class="mt-5 text-center">
            <div>
                <p>© <script>document.write(new Date().getFullYear())</script> {{ $systemSettings->app_name }}. 
                   Developed By <i class="mdi mdi-heart text-danger"></i> {{ $systemSettings->developer }}</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Password visibility toggle
    document.getElementById('password-addon').addEventListener('click', function() {
        var passwordInput = document.querySelector('input[type="password"]');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>
@endsection 