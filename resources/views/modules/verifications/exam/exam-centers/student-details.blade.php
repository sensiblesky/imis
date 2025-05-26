@extends('layouts.auth')

@section('title', 'Exam Center Check In')

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card overflow-hidden">
                                    <div class="bg-primary-subtle">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="text-primary p-3">
                                                    <h5 class="text-primary">Welcome Back !</h5>
                                                    <p>#IAA NEXTLEVO</p>
                                                </div>
                                            </div>
                                            <div class="col-5 align-self-end">
                                                <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="avatar-md profile-user-wid mb-4">
                                                    <img src="{{ $student->photo_base64 }}" alt="" class="img-thumbnail rounded-circle">
                                                </div>
                                                <h5 class="font-size-15 text-truncate">{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</h5>
                                                <p class="text-muted mb-0 text-truncate">{{ $student->username }} <i class="mdi mdi-check-decagram text-primary"></i></p>
                                            </div>

                                            <div class="col-sm-8">
                                                <div class="pt-4">
                                                   
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <h5 class="font-size-15">Campus</h5>
                                                            <p class="text-muted mb-0"><span class="badge rounded-pill badge-soft-primary font-size-11">{{ $student->campus_name }}</span></p>
                                                            
                                                        </div>
                                                        <div class="col-6">
                                                            <h5 class="font-size-15">Program</h5>
                                                            <p class="text-muted mb-0"><span class="badge rounded-pill badge-soft-primary font-size-11">{{ $student->program_name }}</span></p>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="mt-4">
                                                        <a href="javascript: void(0);" class="btn btn-primary waves-effect waves-light btn-sm">View Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card -->

                                <div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Personal Information</h4>

        <p class="text-muted mb-4">
            Hi I'm {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}, enrolled in {{ $student->program_name }} at {{ $student->campus_name }} campus.
        </p>

        <div class="table-responsive">
            <table class="table table-nowrap mb-0">
                <tbody>
                    <tr>
                        <th scope="row">Full Name :</th>
                        <td>{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Username :</th>
                        <td>{{ $student->username }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Program :</th>
                        <td>{{ $student->program_name }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Campus :</th>
                        <td>{{ $student->campus_name }}</td>
                    </tr>
                    
                </tbody>
            </table>
            
        </div>
        <br>
        @if (isset($enrollment))






            <table class="table table-nowrap mb-0">
                <tbody>
                    <tr>
                        <th scope="row">Enrolled Campus :</th>
                        <td><span class="badge rounded-pill badge-soft-primary font-size-11">{{ $enrollment->enrolled_campus }}</span></td>
                    </tr>
                    
                </tbody>
            </table>




            @elseif (isset($availableCenters))
                <form method="POST" action="{{ route('verify.exam.exam-centers.enroll') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $student->user_uid }}">
                    <div class="input-group">
                        <select name="exam_center_id" id="exam_center" class="form-control" required>
                            <option value="">-- Choose Center --</option>
                            @foreach ($availableCenters as $center)
                                @php
                                    $isFull = $center->remaining_slots <= 0;
                                @endphp
                                <option value="{{ $isFull ? '' : $center->id }}" {{ $isFull ? 'disabled' : '' }}>
                                    {{ $center->campus_name }} - {{ $isFull ? 'Currently full' : $center->remaining_slots . ' SPACE AVAILABLE' }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success">Enroll</button>
                    </div>
                </form>
            @else
                <span class="text-danger">No data available</span>
            @endif
            <br>
                        @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <br>
                        <center>
                            <a href="{{ route('verify.exam.exam-centers') }}" class="btn btn-primary">Go Back</a>
                        </center>
                    </div>
                </div>

                                <!-- end card -->
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