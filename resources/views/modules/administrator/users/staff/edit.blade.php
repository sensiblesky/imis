@extends('modules.administrator.components.layouts.app')

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
                            <p>{{ $staff->title  }}. {{ $staff->firstname }} {{ $staff->middlename }} {{ $staff->lastname }}</p>
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
                            <img src="{{ $staff->photo_base64 }}" alt="Profile Photo" class="img-thumbnail rounded-circle">
                        </div>
                        <h5 class="font-size-15 text-truncate">{{ $staff->title  }}. {{ $staff->firstname }} {{ $staff->middlename }} {{ $staff->lastname }}</h5>
                        <p class="text-muted mb-0 text-truncate">{{ $staff->username  }}</p>
                    </div>

                    <div class="col-sm-8">
                        <div class="pt-4">
                            
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="font-size-15">Workspace</h5>
                                    <p class="text-muted mb-0">{{ $staff->workspace_name }}</p>
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
                                <a href="{{ route('admin.users.staff.view', $staff->uid) }}" class="btn btn-primary waves-effect waves-light btn-sm"> <i class="mdi mdi-arrow-left ms-1"></i> Back Profile</a>
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
                                <td>{{ $staff->title  }}. {{ $staff->firstname }} {{ $staff->middlename }} {{ $staff->lastname }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Mobile :</th>
                                <td>{{ $staff->phone }}</td>
                            </tr>
                            <tr>
                                <th scope="row">E-mail :</th>
                                <td>{{ $staff->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Root Campus :</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $staff->campus_name  }} </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Staff Position :</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $staff->position  }} </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Staff Department :</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $staff->department  }} </span></td>
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
                <h4 class="card-title mb-4">Update Staff Informations</h4>
                <form action="{{ route('admin.users.staff.update', $staff->uid) }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-1">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <select name="title" class="form-control">
                                    @foreach($titles as $id => $name)
                                        <option value="{{ $id }}" {{ $staff->base_title_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">First name</label>
                                <input type="text" name="firstname" class="form-control" value="{{ $staff->firstname }}" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Middle name</label>
                                <input type="text" name="middlename" class="form-control" value="{{ $staff->firstname }}" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Last name</label>
                                <input type="text" name="lastname" class="form-control" value="{{ $staff->lastname }}" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                                <div class="invalid-feedback">Please provide a valid email.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ $staff->phone }}" required>
                                <div class="invalid-feedback">Please provide a valid phone number.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="" disabled>Select gender</option>
                                    <option value="M" {{ $staff->gender === 'M' ? 'selected' : '' }}>Male</option>
                                    <option value="F" {{ $staff->gender === 'F' ? 'selected' : '' }}>Female</option>
                                    <option value="NOT SET" {{ $staff->gender === 'NOT SET' ? 'selected' : '' }}>Not Set</option>
                                </select>
                                <div class="invalid-feedback">Please select a gender.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Disability</label>
                                <select class="select2 form-control select2-multiple" name="disability_ids[]" multiple required data-placeholder="Choose...">
                                    @foreach($disabilities as $id => $name)
                                        <option value="{{ $id }}" {{ in_array($id, $staffDisabilities ?? []) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $staff->username }}" required>
                                <div class="invalid-feedback">Username is required.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ $staff->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $staff->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ $staff->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                <div class="invalid-feedback">Please choose a status.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Default Campus</label>
                                <select name="root_campus" class="form-select" required>
                                    <option value="" disabled>Select Root Campus</option>
                                    @foreach($allCampuses as $id => $name)
                                        <option value="{{ $id }}" {{ $staff->campus_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Assign Many Campuses</label>
                                <select class="select2 form-control select2-multiple" name="campuses[]" multiple>
                                    @foreach($allCampuses as $id => $name)
                                        <option value="{{ $id }}" {{ in_array($id, $userCampuses) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                            <label class="form-label">Default Workspace</label>
                            <select class="form-select" name="default_workspace">
                                @foreach($assignedWorkspaces as $id => $display_name)
                                    <option value="{{ $id }}" {{ $staff->default_workspace == $id ? 'selected' : '' }}>
                                        {{ $display_name }}
                                    </option>
                                @endforeach
                            </select>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Assigned Workspaces</label>
                                <select class="form-select select2" name="workspaces[]" multiple>
                                    @foreach($allWorkspaces as $id => $name)
                                        <option value="{{ $id }}" {{ in_array($id, $userWorkspaces) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <select name="base_department_id" class="form-control">
                                    @foreach($departments as $id => $name)
                                        <option value="{{ $id }}" {{ $staff->base_department_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">Position</label>
                                <select name="base_position_id" class="form-control">
                                    @foreach($positions as $id => $name)
                                        <option value="{{ $id }}" {{ $staff->base_position_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Photo</label><br>
                        <input type="file" name="photo" class="form-control">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="YES" name="loginlimit" {{ $staff->loginlimit === 'YES' ? 'checked' : '' }}>
                        <label class="form-check-label">Limit login to one device</label>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group auth-pass-inputgroup">
                                    <input type="password" name="password" class="form-control" autocomplete="new-password" aria-label="Password" aria-describedby="password-addon">
                                    <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group auth-pass-inputgroup">
                                    <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" aria-label="Password" aria-describedby="password-addon">
                                    <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                    <div>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-check-double font-size-16 align-middle me-2"></i> Update Profile
                        </button>
                    </div>
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
    

    <!-- form wizard init -->
    <script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>


    <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3IzvxQBVkRxP4VugwlchGJXL71MM__dQ&callback=initMap"
    defer></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>


    <script>
        let googleMap;
        let directionsService;
        let directionsRenderer;
        let googleMarker;

        function showGoogleMap(destLat, destLng, label = 'Login Location') {
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('exampleModalFullscreen'));
            modal.show();

            setTimeout(() => {
                const mapContainer = document.getElementById('google-map');

                // Initialize map if not already
                if (!googleMap) {
                    googleMap = new google.maps.Map(mapContainer, {
                        zoom: 18,
                        center: { lat: destLat, lng: destLng },
                        mapTypeId: 'satellite'
                    });
                } else {
                    googleMap.setCenter({ lat: destLat, lng: destLng });
                }

                // Remove any existing marker
                if (googleMarker) {
                    googleMarker.setMap(null);
                }

                // Initialize directions service/renderer
                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    map: googleMap,
                    suppressMarkers: false,
                    polylineOptions: {
                        strokeColor: '#4285F4',
                        strokeWeight: 6
                    }
                });

                // Try to get user's current location
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const userLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };

                            const destination = {
                                lat: destLat,
                                lng: destLng
                            };

                            // Request directions
                            directionsService.route({
                                origin: userLocation,
                                destination: destination,
                                travelMode: google.maps.TravelMode.DRIVING,
                            }, (response, status) => {
                                if (status === google.maps.DirectionsStatus.OK) {
                                    directionsRenderer.setDirections(response);
                                } else {
                                    alert('Could not get route: ' + status);
                                    fallbackToMarker(destLat, destLng, label);
                                }
                            });
                        },
                        (error) => {
                            alert("Could not get your location: " + error.message);
                            fallbackToMarker(destLat, destLng, label);
                        }
                    );
                } else {
                    alert("Geolocation not supported by your browser.");
                    fallbackToMarker(destLat, destLng, label);
                }
            }, 300);
        }

        function fallbackToMarker(lat, lng, label = 'Login Location') {
            // Show just the destination with a marker
            googleMap.setCenter({ lat: lat, lng: lng });
            googleMarker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: googleMap,
                title: label
            });
        }

        function initMap() {
            const location = { lat: -6.7924, lng: 39.2083 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: location,
                mapTypeId: 'satellite'
            });

            new google.maps.Marker({
                position: location,
                map: map,
            });
        }
    </script>


<script>
    document.querySelectorAll('.terminate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission

            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to terminate this session?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, terminate it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    form.submit();
                }
            });
        });
    });
</script>

<script>
    function handleToggleAttempt() {
        Swal.fire({
            icon: 'warning',
            title: 'Action Not Allowed',
            text: 'Admins cannot enable or disable Two Factor Authentication for users. The user must configure it themselves.',
            confirmButtonText: 'Okay'
        });
    }
</script>

<script>
    function showComparisonModal(oldBase64, newBase64) {
        try {
            const oldJson = JSON.parse(atob(oldBase64));
            const newJson = JSON.parse(atob(newBase64));

            let htmlContent = `
                <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="background-color:#e2e3ff;" style="width: 40%">Field</th>
                            <th style="background-color:#f8d7da;">Old Value</th>
                            <th style="background-color:#d4edda;">New Value</th>
                        </tr>
                    </thead>
                    <tbody>`;

            // Collect all unique keys from both old and new
            const allKeys = new Set([...Object.keys(oldJson), ...Object.keys(newJson)]);
            for (const key of allKeys) {
                htmlContent += `
                    <tr>
                        <td style="background-color:#e2e3ff;"><strong>${key}</strong></td>
                        <td>${oldJson[key] ?? '<em>null</em>'}</td>
                        <td>${newJson[key] ?? '<em>null</em>'}</td>
                    </tr>`;
            }

            htmlContent += `</tbody></table></div>`;

            Swal.fire({
                title: 'Change Logs',
                html: htmlContent,
                width: '80%',
                confirmButtonText: 'Close',
                customClass: {
                    popup: 'text-start'
                }
            });
        } catch (e) {
            Swal.fire('Error', 'Failed to parse or display JSON data', 'error');
        }
    }
</script>




@endpush