@extends('modules.support.components.layouts.app')

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('title', 'Profile')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
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
                            <h5 class="text-primary">Welcome Back To</h5>
                            <p> {{ $student->title_name }}. {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</p>
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
                            <img src="{{ $student->photo_base64 }}" alt="Profile Photo" class="img-thumbnail rounded-circle">
                        </div>
                        <h5 class="font-size-15 text-truncate">{{ $student->title_name }}. {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</h5>
                        <p class="text-muted mb-0 text-truncate">{{ $student->username  }}</p>
                    </div>

                    <div class="col-sm-8">
                        <div class="pt-4">
                            
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="font-size-15">Workspace</h5>
                                    <p class="text-muted mb-0">Student Only</p>
                                </div>
                                <div class="col-6">
                                    <h5 class="font-size-15">Multi Campus</h5>
                                    Prohibited
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('support.users.student.view', $student->uid) }}" class="btn btn-primary waves-effect waves-light btn-sm"> <i class="mdi mdi-arrow-left ms-1"></i> Back Profile</a>
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
                                <td>{{ $student->title_name  }}. {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Mobile :</th>
                                <td>{{ $student->phone }}</td>
                            </tr>
                            <tr>
                                <th scope="row">E-mail :</th>
                                <td>{{ $student->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Root Campus :</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $student->campus_name  }} </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Student Level :</th>
                                <td><span class="badge badge-soft-success font-size-12">{{ $student->level_name  }}  </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Student Course :</th>
                                <td><span class="badge badge-soft-success font-size-12">{{ $student->program_name  }}  </span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>         
    
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Update Student Informations</h4>
                <form action="{{ route('support.users.student.update', $student->uid) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('POST') <!-- Use PUT method for editing -->
                    
                    {{-- Row 1: Title, First name, Last name --}}
                    <div class="row">
                        <div class="col-md-1">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <select name="title" class="form-select" required>
                                    <option disabled selected value="">Choose...</option>
                                    @foreach($titles as $id => $name)
                                        <option value="{{ $id }}" {{ $student->title == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a title.</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="firstname" class="form-control" value="{{ old('firstname', $student->firstname) }}" placeholder="First name" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middlename" class="form-control" value="{{ old('middlename', $student->middlename) }}" placeholder="Middle name">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="lastname" class="form-control" value="{{ old('lastname', $student->lastname) }}" placeholder="Last name" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Username / Reg No / PF No</label>
                                <input type="text" name="username" class="form-control" value="{{ old('username', $student->username) }}" placeholder="Username" required>
                                <div class="invalid-feedback">Please provide a username.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}" placeholder="Email" required>
                                <div class="invalid-feedback">Please provide a valid email.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Phone, Gender, Photo --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="number" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}" placeholder="Phone">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option selected disabled value="">Choose...</option>
                                    <option value="M" {{ $student->gender == 'M' ? 'selected' : '' }}>Male</option>
                                    <option value="F" {{ $student->gender == 'F' ? 'selected' : '' }}>Female</option>
                                    <option value="NOT SET" {{ $student->gender == 'NOT SET' ? 'selected' : '' }}>Not Set</option>
                                </select>
                                <div class="invalid-feedback">Please select a gender.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <div class="form-text">Optional. JPG, PNG, WebP only.</div>
                                @if ($student->photo)
                                    <img src="{{ asset('storage/'.$student->photo) }}" alt="Student Photo" class="img-thumbnail mt-2" width="100">
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Row 4: Level, Program, Disabilities --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <select name="level_id" class="form-select" required id="level_id">
                                    <option disabled selected value="">Choose a level</option>
                                    @foreach($level as $id => $name)
                                        <option value="{{ $id }}" {{ $student->level_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a valid level.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Program</label>
                                <select name="program_id" class="form-select select2" required id="program_id">
                                    <option disabled selected value="">Choose a program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ $student->program_id == $program->id ? 'selected' : '' }}>{{ $program->program_name }}</option>
                                    @endforeach

                                </select> <!-- ✅ Correctly close the <select> here -->
                                <div class="invalid-feedback">Please select a program.</div>
                            </div>

                        </div>
                        <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Disabilities</label>
                        <select name="disability_ids[]" multiple class="form-select select2">
                    @foreach($disabilities as $id => $name)
                        <option value="{{ $id }}" {{ in_array($id, $studentDisabilities) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>

                    </div>
                </div>

                    </div>

                    {{-- Row 5: Campus & Workspace --}}
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label"> Campus</label>
                                <select name="root_campus" class="form-select select2" required>
                                    <option selected disabled value="">Choose...</option>
                                    @foreach($allCampuses as $id => $name)
                                        <option value="{{ $id }}" {{ $student->campus_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a campus.</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Academic Year</label>
                                <select name="academic_year_id" class="form-select select2" required>
                                    <option selected disabled value="">Choose...</option>
                                    @foreach($academicYears as $id => $name)
                                        <option value="{{ $id }}" {{ $student->academic_year_id == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                        <option value="{{ $id }}" {{ $student->intake_id == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                        <option value="{{ $id }}" {{ $student->valid_until_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select academic year.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select select2">
                                    <option value="active" {{ $student->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $student->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ $student->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Next Of Kin --}}
                    <br><h5 class="font-size-14">Next Of Kin (optional)</h5>
                    <div class="progress bg-transparent progress-sm">
                        <div class="progress-bar bg-primary rounded" role="progressbar" style="width: 100%" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100"></div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Parent Names</label>
                                <input type="text" name="parent_fullname" class="form-control" value="{{ old('parent_fullname', $student->parent_fullname) }}" placeholder="First name" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Parent Phone Number</label>
                                <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}" placeholder="Middle name">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Parent / Guardian Relationship</label>
                                <select name="relationship_type" class="form-select" required>
                                    <option disabled selected value="">Choose...</option>
                                    @foreach($relationship as $id => $name)
                                        <option value="{{ $id }}" {{ $student->relationship_type == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a relationship.</div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <button type="submit" class="btn btn-primary float-end waves-effect waves-light">
                        <i class="bx bx-check-double font-size-16 align-middle me-2"></i> Update Student Account
                    </button>
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
    <!-- jquery step -->
    <script src="{{ asset('assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>


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