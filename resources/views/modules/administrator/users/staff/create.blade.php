@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Dashboard')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Staff Users</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Users</a></li>
                        <li class="breadcrumb-item active">Staff</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

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


    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="row mb-3">
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="mt-2">
                                            <h5>Manage Users</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-9 col-sm-6">
                                        <div class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                            <a href="{{ route('admin.users.staff') }}" class="btn btn-primary btn-sm me-2"><i class="ri-arrow-left-line"></i> Back to Staff Users</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <form action="{{ route('admin.users.staff.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                    @csrf

                                    {{-- Row 1: Title, First name, Last name --}}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Title</label>
                                                <select name="title" class="form-select" required>
                                                    <option disabled selected value="">Choose...</option>
                                                    @foreach($titles as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a title.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">First Name</label>
                                                <input type="text" name="firstname" class="form-control" placeholder="First name" required>
                                                <div class="valid-feedback">Looks good!</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Middle Name</label>
                                                <input type="text" name="middlename" class="form-control" placeholder="Middle name">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" name="lastname" class="form-control" placeholder="Last name" required>
                                                <div class="valid-feedback">Looks good!</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Username / Reg No / PF No</label>
                                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                                                <div class="invalid-feedback">Please provide a username.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                                                <div class="invalid-feedback">Please provide a valid email.</div>
                                            </div>
                                        </div>
                                    </div>



                                    {{-- Row 3: Phone, Gender, Photo --}}
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="number" name="phone" class="form-control" placeholder="Phone">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Gender</label>
                                                <select name="gender" class="form-select" required>
                                                    <option selected disabled value="">Choose...</option>
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                    <option value="NOT SET">Not Set</option>
                                                </select>
                                                <div class="invalid-feedback">Please select a gender.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Photo</label>
                                                <input type="file" name="photo" class="form-control" accept="image/*">
                                                <div class="form-text">Optional. JPG, PNG, WebP only.</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Row 4: Staff Position, Department, Disabilities --}}
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Position</label>
                                                <select name="position_id" class="form-select" required>
                                                    <option disabled selected value="">Choose a position</option>
                                                    @foreach($positions as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a valid staff position.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Department</label>
                                                <select name="department_id" class="form-select" required>
                                                    <option disabled selected value="">Choose...</option>
                                                    @foreach($departments as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a department.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Disabilities</label>
                                                <select name="disability_ids[]" multiple class="form-select select2">
                                                    @foreach($disabilities as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Row 5: Campus & Workspace --}}
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Default Campus</label>
                                                <select name="root_campus" class="form-select" required>
                                                    <option selected disabled value="">Choose...</option>
                                                    @foreach($allCampuses as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a default campus.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Multiple Campuses</label>
                                                <select name="campuses[]" multiple class="form-select select2">
                                                    @foreach($allCampuses as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="active" selected>Active</option>
                                                    <option value="inactive">Inactive</option>
                                                    <option value="suspended">Suspended</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Workspaces</label>
                                                <select name="workspaces[]" multiple class="form-select select2">
                                                    @foreach($allWorkspaces as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- Confirm and Submit --}}
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" value="" id="termsCheck" required>
                                        <label class="form-check-label" for="termsCheck">
                                            Confirm that all details are correct.
                                        </label>
                                        <div class="invalid-feedback">You must confirm before submitting.</div>
                                    </div>

                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="bx bx-check-double font-size-16 align-middle me-2"></i> Create Staff Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
                <!-- end w-100 -->
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
@endpush