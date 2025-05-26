@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Auth Logs View')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Auth Log View</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item">Auth Logs</li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary-subtle">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">Login Activity Summary!</h5>
                                <p>This is login log information</p>
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
                                <img src="{{ $user && $user->photo ? $user->photo : asset('assets/images/users/avatar-1.jpg') }}" alt="" class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15 text-truncate">
                                {{ $user ? "{$user->firstname} {$user->middlename} {$user->lastname}" : 'User Not Found' }}
                            </h5>
                            <p class="text-muted mb-0 text-truncate">{{ $userNotFound ? 'No associated user' : 'System User' }}</p>
                        </div>

                    @if (!$userNotFound)
                        <div class="col-sm-8">
                            <div class="pt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">{{ $log->status }}</h5>
                                        <p class="text-muted mb-0">Status</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">{{ $log->created_at }}</h5>
                                        <p class="text-muted mb-0">Attempted At</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    @if ($user->role_id == 1)
                                        <a href="{{ route('admin.users.staff.view', ['uid' => $user->uid]) }}"
                                        class="btn btn-primary waves-effect waves-light btn-sm">
                                            View Profile <i class="mdi mdi-arrow-right ms-1"></i>
                                        </a>
                                    @elseif ($user->role_id == 2)
                                        <a href="{{ route('admin.users.student.view', ['uid' => $user->uid]) }}"
                                        class="btn btn-primary waves-effect waves-light btn-sm">
                                            View Profile <i class="mdi mdi-arrow-right ms-1"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-warning btn-sm" title="Unknown user role">
                                            Role Unknown
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- end card -->

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Login Attempt Details</h4>
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr><th scope="row">IP Address:</th><td>{{ $log->ip_address }}</td></tr>
                            <tr><th scope="row">Browser:</th><td>{{ $log->browser }}</td></tr>
                            <tr><th scope="row">Platform:</th><td>{{ $log->platform }}</td></tr>
                            <tr><th scope="row">Device Type:</th><td>{{ $log->device_type }}</td></tr>
                            <tr><th scope="row">Location:</th><td>{{ $log->city }}, {{ $log->region }}, {{ $log->country }}</td></tr>
                            <tr><th scope="row">ISP:</th><td>{{ $log->isp }}</td></tr>
                            <tr><th scope="row">Timezone:</th><td>{{ $log->timezone }}</td></tr>
                            <tr><th scope="row">Status:</th><td>{{ $log->status }}</td></tr>
                            <tr><th scope="row">Username:</th><td>{{ $log->username }}</td></tr>
                            <tr><th scope="row">Password:</th><td>{{ $log->password }}</td></tr>
                            <tr><th scope="row">User Agent:</th><td>{{ $log->user_agent }}</td></tr>
                            <tr><th scope="row">Cordinates:</th><td>{{ $log->latitude }} {{ $log->longitude }}</td></tr>
                            
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
                    <h4 class="card-title mb-4">Map View</h4>
                    @if($log->latitude && $log->longitude)
                        <div id="map-loader" class="alert alert-info">Loading location on map...</div>
                        <div id="map" style="height: 800px; display: none;"></div>

                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $log->latitude }},{{ $log->longitude }}" 
                        target="_blank" class="btn btn-primary mt-2">
                            Open in Google Maps
                        </a>

                        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3IzvxQBVkRxP4VugwlchGJXL71MM__dQ"></script>
                        <script>
                            window.addEventListener('load', function () {
                                const target = { lat: parseFloat("{{ $log->latitude }}"), lng: parseFloat("{{ $log->longitude }}") };

                                const map = new google.maps.Map(document.getElementById('map'), {
                                    zoom: 17,
                                    center: target,
                                    mapTypeId: 'hybrid', // Satellite with labels
                                    mapTypeControl: true,
                                    zoomControl: true,
                                    fullscreenControl: true,
                                    streetViewControl: false,
                                    styles: [
                                        {
                                            featureType: "poi",
                                            stylers: [{ visibility: "off" }]
                                        },
                                        {
                                            featureType: "transit",
                                            stylers: [{ visibility: "off" }]
                                        },
                                        {
                                            featureType: "road",
                                            stylers: [{ visibility: "simplified" }]
                                        }
                                    ]
                                });

                                new google.maps.Marker({
                                    position: target,
                                    map: map,
                                    title: 'Target Location'
                                });

                                document.getElementById('map-loader').style.display = 'none';
                                document.getElementById('map').style.display = 'block';
                            });
                        </script>
                    @else
                        <div class="alert alert-warning" title="Coordinates not found. No map to display.">
                            <strong>Coordinates not found</strong><br>Cannot show map.
                        </div>
                    @endif



                    

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
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
 
@endpush