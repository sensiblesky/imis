@extends('modules.support..components.layouts.app')

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush 

@section('title', 'View Staff')

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

    @if($staff->is_deleted)
        <div class="alert alert-danger">
            <strong>Alert!</strong> This user has been deleted on {{ $staff->is_deleted_at  }}, Please if this is suspisous activity, take legal action as soon as possible.
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
                                <a href="{{ route('support.users.staff.edit', $staff->uid) }}" class="btn btn-primary waves-effect waves-light btn-sm">Edit Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
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

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Account Security</h4>

                <div class="col-lg-12">
                    <div class="card mini-stats-wid">
                            
                        

                        <div class="bg-light p-3 d-flex mb-3 rounded">
                            <img src="#" alt="" class="avatar-sm rounded me-3">
                            <div class="flex-grow-1">
                                <h5 class="font-size-15 mb-2"><a href="#" class="text-body">Two Factor Authentication</a> 
                                    <span class="badge badge-soft-info">{{ $staff->two_factor_status ? 'Enabled' : 'Disabled' }}</span>
                                </h5>
                                <p class="mb-0 text-muted"><i class="bx bx-map text-body align-middle"></i> 
                                    @if ($staff->two_factor_method === 'email')
                                        <a href="#">Email Two Factor Authentication</a>
                                    @elseif ($staff->two_factor_method === 'google')
                                        <a href="#">Google Authenticator method</a>
                                    @else
                                        Not Set
                                    @endif
                                </p>
                            </div>
                            <div>
                                <div class="square-switch">
                                    <!-- Check if 2FA is enabled and set the toggle accordingly -->
                                    <input type="checkbox"
                                        id="square-switch1"
                                        switch="none"
                                        {{ $staff->two_factor_status == 1 ? 'checked' : '' }}
                                        disabled
                                    />
                                    <label for="square-switch1"
                                        data-on-label="On"
                                        data-off-label="Off"
                                        onclick="handleToggleAttempt()">
                                    </label>


                                </div>
                            </div>
                        </div>



                        

                    </div>
                </div>
            </div>
        </div>
    </div>         
    
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">LoggedIn Active Session</h4>
                <p class="text-muted mb-4">You are currently logged in from the following devices. If you don't recognize any of these devices, you can terminate the session.</p>
                <div class="table-responsive">
                    <table id="datatable2" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Last Activity</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($sessions as $index => $session)
                            <tr>
                                <td>{{ $index + 1 }}</td> {{-- Session # --}}
                                <td>{{ $session['ip_address'] }}</td>
                                <td>{{ $session['user_agent'] }}</td>
                                <td>{{ $session['last_activity'] }}</td>
                                <td>
                                <form method="POST" action="{{ route('support.users.staff.terminateSession', $staff->uid) }}" style="display:inline" class="terminate-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="session_id" value="{{ $session['id'] }}">
                                    <button type="submit" class="btn btn-sm btn-danger">Terminate</button>
                                </form>


                                    @if($session['most_recent'])
                                        <span class="badge bg-info ms-2">Most Recent</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No active sessions found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Login Activities</h4>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>Credentials</th>
                                <th>Status</th>
                                <th>Platform</th>
                                <th>Browser</th>
                                <th>Location</th>
                                <th>ISP</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loginLogs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>
                                        Username: {{ $log->username }}<br>
                                        Password: {{ $log->password }}<br>    
                                    <br>
                                    <td>
                                        <span class="badge badge-soft-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->platform }}</td>
                                    <td>{{ $log->browser }} {{ $log->browser }}</td>
                                    <td>
                                        {{ $log->city }}, {{ $log->country }}<br>
                                        @if($log->latitude && $log->longitude)
                                            <span class="badge badge-soft-success">{{ $log->longitude }}</span>
                                            <span class="badge badge-soft-danger">{{ $log->latitude }}</span><br>

                                            <span class="badge badge-soft-danger">
                                                <a href="#" onclick="showGoogleMap({{ $log->latitude }}, {{ $log->longitude }})" class="text-decoration-none">Track Location</a>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $log->asy }} </td>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty
                                
                            @endforelse
                        </tbody>
                    </table>


                    <!-- Fullscreen modal -->
                    <div id="exampleModalFullscreen" class="modal fade" tabindex="-1" aria-labelledby="#exampleModalFullscreenLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenLabel">Login Location</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="google-map" style="height: 80vh; width: 100%;"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end modal -->

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">General Activities</h4>
                <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100"">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>Action</th>
                                <th>Changes</th>
                                <th>Platform</th>
                                <th>Browser</th>
                                <th>Location</th>
                                <th>ISP</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($GeneralLogs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>
                                        {{ $log->action }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="showComparisonModal('{{ base64_encode($log->old_data) }}', '{{ base64_encode($log->new_data) }}')">
                                            View Changes
                                        </button>
                                    </td>
                                    <td>{{ $log->platform }}</td>
                                    <td>{{ $log->browser }} {{ $log->browser }}</td>
                                    <td>
                                        {{ $log->city }}, {{ $log->country }}<br>
                                        @if($log->latitude && $log->longitude)
                                            <span class="badge badge-soft-success">{{ $log->longitude }}</span>
                                            <span class="badge badge-soft-danger">{{ $log->latitude }}</span><br>
                                            <span class="badge badge-soft-danger">
                                                <a href="#" onclick="showGoogleMap({{ $log->latitude }}, {{ $log->longitude }})" class="text-decoration-none">Track Location</a>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $log->asy }} </td>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty

                            @endforelse
                        </tbody>
                    </table>
                    <!-- Fullscreen modal -->
                    <div id="exampleModalFullscreen" class="modal fade" tabindex="-1" aria-labelledby="#exampleModalFullscreenLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenLabel">Login Location</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="google-map" style="height: 80vh; width: 100%;"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end modal -->
                </div>
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
            // If base64 is null/empty, treat it as '{}'
            const oldJson = oldBase64 ? JSON.parse(atob(oldBase64)) : {};
            const newJson = newBase64 ? JSON.parse(atob(newBase64)) : {};

            let htmlContent = `
                <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="background-color:#e2e3ff;">Field</th>
                            <th style="background-color:#f8d7da;">Old Value</th>
                            <th style="background-color:#d4edda;">New Value</th>
                        </tr>
                    </thead>
                    <tbody>`;

            const allKeys = new Set([...Object.keys(oldJson), ...Object.keys(newJson)]);
            for (const key of allKeys) {
                htmlContent += `
                    <tr>
                        <td style="background-color:#e2e3ff;"><strong>${key}</strong></td>
                        <td>${newJson[key] !== undefined ? formatValue(newJson[key]) : '<em>null</em>'}</td>
                        <td>${oldJson[key] !== undefined ? formatValue(oldJson[key]) : '<em>null</em>'}</td>
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
    function formatValue(value) {
    if (value === null) return '<em>null</em>';
    if (typeof value === 'object') return `<pre>${JSON.stringify(value, null, 2)}</pre>`;
    return value;
}

</script>




@endpush