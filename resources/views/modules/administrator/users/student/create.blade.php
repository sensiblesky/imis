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
                                <form action="{{ route('admin.users.student.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
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

                                    {{-- Row 4: STUDENT LEVEL PROGRAM, Department, Disabilities --}}
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Level</label>
                                                <select name="level_id" class="form-select" required id="level_id">
                                                    <option disabled selected value="">Choose a level</option>
                                                    @foreach($level as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a valid level.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Program</label>
                                                <select name="program_id" class="form-select select2" required id="program_id">
                                                    <option disabled selected value="">Choose...</option>
                                                </select>
                                                <div class="invalid-feedback">Please select a program.</div>
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
                                                <select name="root_campus" class="form-select select2" required>
                                                    <option selected disabled value="">Choose...</option>
                                                    @foreach($allCampuses as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a default campus.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Academic Year</label>
                                                <select name="academic_year" class="form-select select2" required>
                                                    <option selected disabled value="">Choose...</option>
                                                    @foreach($academicYears as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select academic year.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Intake</label>
                                                <select name="intake_id" class="form-select select2" required>
                                                    <option selected disabled value="">Choose...</option>
                                                    @foreach($intakes as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select academic year.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Valid Until</label>
                                                <select name="valid_until_id" class="form-select select2" required>
                                                    <option selected disabled value="">Choose...</option>
                                                    @foreach($validUntil as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select academic year.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select select2">
                                                    <option value="active" selected>Active</option>
                                                    <option value="inactive">Inactive</option>
                                                    <option value="suspended">Suspended</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>







                                    <br></br>
                                    <h5 class="font-size-14">Next Of Kin (optional)</h5>
                                    <div class="progress bg-transparent progress-sm">
                                        <div class="progress-bar bg-primary rounded" role="progressbar" style="width: 100%" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div><br>
                                        <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Parent Names</label>
                                                <input type="text" name="parent_fullname" class="form-control" placeholder="First name" required>
                                                <div class="valid-feedback">Looks good!</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Parent Phone Number</label>
                                                <input type="text" name="parent_phone" class="form-control" placeholder="Middle name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="parent_email" class="form-control" placeholder="Email" required>
                                                <div class="invalid-feedback">Please provide a valid email.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Relationship</label>
                                                <select name="relationship_type" class="form-select select2" required>
                                                    <option disabled selected value="">Choose...</option>
                                                    @foreach($relationships as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                    </div><br></br>


                                    <div>
                                        <div class="row mb-3">
                                            <div class="col-xl-3 col-sm-6">
                                                <div class="mt-2">
                                                    <h5>Insuarance (optional)</h5>
                                                    
                                                </div>
                                                
                                            </div>
                                            <div class="progress bg-transparent progress-sm">
                                                <div class="progress-bar bg-primary rounded" role="progressbar" style="width: 100%" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div><br>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label class="form-label">Insuarance company</label>
                                                <select name="insurance_company" class="form-select select2" required>
                                                    <option disabled selected value="">Choose...</option>
                                                    @foreach($insurances as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please provide a password.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Insuarance Number</label>
                                                <input type="number" name="nhif_id_number" class="form-control" placeholder="card number" required>
                                                <div class="invalid-feedback">Please enter card number.</div>
                                            </div>
                                        </div>
                                    </div><br></br>













                                    <div>
                                        <div class="row mb-3">
                                            <div class="col-xl-3 col-sm-6">
                                                <div class="mt-2">
                                                    <h5>Account Security (optional)</h5>
                                                    
                                                </div>
                                                
                                            </div>
                                            <div class="progress bg-transparent progress-sm">
                                                <div class="progress-bar bg-primary rounded" role="progressbar" style="width: 100%" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div><br>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label class="form-label">Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                                <div class="invalid-feedback">Please provide a password.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                                                <div class="invalid-feedback">Please confirm your password.</div>
                                            </div>
                                        </div>
                                    </div>






                                    
                                        
                                    


                                    {{-- Confirm and Submit --}}
                                    <button type="submit" class="btn btn-primary float-end waves-effect waves-light">
                                        <i class="bx bx-check-double font-size-16 align-middle me-2"></i> Create Student Account
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
    <script>
        // Pass the programs from PHP to JavaScript
        var programs = @json($programs);

        // Function to populate the program dropdown based on the selected level_id
        function populatePrograms() {
            var levelId = document.getElementById('level_id').value;
            var programSelect = document.getElementById('program_id');
            
            // Clear existing options
            programSelect.innerHTML = '<option disabled selected value="">Choose...</option>';

            // Filter programs based on the selected level_id
            var filteredPrograms = programs.filter(function(program) {
                return program.level_id == levelId;
            });

            // Populate the program dropdown with the filtered programs
            filteredPrograms.forEach(function(program) {
                var option = document.createElement('option');
                option.value = program.id;
                option.text = program.program_name;
                programSelect.appendChild(option);
            });
        }

        // Add event listener to update the program dropdown when the level changes
        document.getElementById('level_id').addEventListener('change', populatePrograms);
    </script>
@endpush