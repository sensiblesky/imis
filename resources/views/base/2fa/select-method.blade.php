@extends('modules.administrator.components.layouts.app')

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush 

@section('title', 'Profile')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<div class="row">
    <div class="col-xl-4">
        <div class="card overflow-hidden">
            <div class="bg-primary-subtle">
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-3">
                            <h5 class="text-primary">Welcome Back !</h5>
                            <p>{{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}</p>
                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="avatar-md profile-user-wid mb-4">
                            <img src="{{ $user->photo_base64 }}" alt="Profile Photo" class="img-thumbnail rounded-circle">
                        </div>
                        <h5 class="font-size-15 text-truncate">{{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}</h5>
                        <p class="text-muted mb-0 text-truncate">{{ $user->username  }}</p>
                    </div>

                    <div class="col-sm-8">
                        <div class="pt-4">
                            
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="font-size-15">Workspace</h5>
                                    <p class="text-muted mb-0">{{ $user->workspace_name }}</p>
                                </div>
                                <div class="col-6">
                                    <h5 class="font-size-15">Multi Campus</h5>
                                    @php
                                        $badgeColors = ['badge-soft-success', 'badge-soft-danger', 'badge-soft-primary', 'badge-soft-warning', 'badge-soft-info'];
                                    @endphp
                                    @forelse($campuses as $index => $campus)
                                        <span class="badge {{ $badgeColors[$index % count($badgeColors)] }} font-size-12">
                                            {{ strtoupper($campus) }}
                                        </span>
                                    @empty
                                        <span class="text-muted">No Multiple campuses assigned</span>
                                    @endforelse
                                    <!-- <p class="text-muted mb-0"></p> -->
                                    
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary waves-effect waves-light btn-sm">Edit Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end card -->

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Personal Information</h4>

                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Full Name :</th>
                                <td>{{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Mobile :</th>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <th scope="row">E-mail :</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Root Campus :</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $user->campus_name  }} </span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end card -->
    </div>         
    
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
            <h3>Select Two-Factor Authentication Method</h3>

            <form action="{{ route('twofa.select-method') }}" method="POST" id="select-method-form">
                @csrf

                <div class="mb-3">
                    <label for="method">Choose a 2FA method:</label>
                    <select name="method" id="method" class="form-select" required>
                        <option value="">-- Select method --</option>
                        <option value="email">Email OTP</option>
                        <option value="google">Google Authenticator</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="email-input-section">
                    <label for="email">Enter your email address:</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com">
                </div>

                @error('method')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-primary mt-3">Continue</button>
            </form>
            </div>
        </div>
        
    </div>
</div>
<!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const methodSelect = document.getElementById("method");
    const emailSection = document.getElementById("email-input-section");

    methodSelect.addEventListener("change", function () {
        const selected = this.value;

        if (selected === "email") {
            emailSection.classList.remove("d-none");
        } else {
            emailSection.classList.add("d-none");
        }
    });
});
</script>
@endpush