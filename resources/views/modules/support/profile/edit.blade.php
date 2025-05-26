@extends('modules.support..components.layouts.app')

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
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
                                <a href="{{ route('support.profile') }}" class="btn btn-primary waves-effect waves-light btn-sm">View Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
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
                <h4 class="card-title mb-4">Update Profile</h4>
                <form class="needs-validation" method="POST" enctype="multipart/form-data" action="{{ route('support.profile.update') }}" novalidate>
                    @csrf
                    <div class="row">
                        <!-- Firstname -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">First name</label>
                                <input type="text" name="firstname" class="form-control" value="{{ $user->firstname }}" required>
                            </div>
                        </div>
                        <!-- Middlename -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Middle name</label>
                                <input type="text" name="middlename" class="form-control" value="{{ $user->middlename }}">
                            </div>
                        </div>
                        <!-- Lastname -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Last name</label>
                                <input type="text" name="lastname" class="form-control" value="{{ $user->lastname }}" required>
                            </div>
                        </div>
                        <!-- Username -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Username </label>
                                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                        </div>
                        <!-- Phone -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                            </div>
                        </div>
                        <!-- Gender -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="M" {{ $user->gender == 'M' ? 'selected' : '' }}>Male</option>
                                    <option value="F" {{ $user->gender == 'F' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        
                        <!-- Photo -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control">
                            </div>
                        </div>


                        <!-- Default Campus -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Default Campus</label>
                                <select class="select2 form-control" name="default_campus" data-placeholder="Choose...">
                                    <option value="">Select Default Campus</option>
                                    @foreach ($allCampuses as $campus)
                                        <option value="{{ $campus->id }}" {{ $campus->id == $user->campus_id ? 'selected' : '' }}>
                                            {{ $campus->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- Default Workspace -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Default Workspace</label>
                                <select class="select2 form-control" name="default_workspace" data-placeholder="Choose...">
                                    <option value="">Select Default Workspace</option>
                                    @foreach ($allWorkspaces as $workspace)
                                        <option value="{{ $workspace->id }}" {{ $workspace->id == $user->default_workspace ? 'selected' : '' }}>
                                            {{ $workspace->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button class="btn btn-primary" type="submit">Update Profile</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
@endpush