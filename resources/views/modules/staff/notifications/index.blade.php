@extends('modules.staff.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Notifications')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Notifications List</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Notifications</a></li>
                        <li class="breadcrumb-item active">index</li>
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


    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex align-items-center">
                <img src="{{ $authUserPhoto }}" alt="" class="avatar-sm rounded">
                <div class="ms-3 flex-grow-1">
                    <h5 class="mb-2 card-title">Hello, {{ $authUserName }}</h5>
                    <p class="text-muted mb-0">
                        Here you can create, view, edit, and delete notifications.
                        Use the buttons above to manage your notifications.
                    </p>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->


    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-xl-3 col-sm-6">
                                    <div class="mt-2">
                                        <h5>Manage Notifications</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        @php
                                            $badgeColors = [
                                                'badge-soft-primary',
                                                'badge-soft-success',
                                                'badge-soft-danger',
                                                'badge-soft-warning',
                                                'badge-soft-info',
                                                'badge-soft-dark',
                                                'badge-soft-secondary',
                                                'badge-soft-light',
                                            ];

                                            $typeColors = [
                                                'info' => 'badge-soft-info',
                                                'success' => 'badge-soft-success',
                                                'warning' => 'badge-soft-warning',
                                                'error' => 'badge-soft-danger',
                                                'alert' => 'badge-soft-dark',
                                                'notice' => 'badge-soft-primary',
                                            ];
                                        @endphp

                                        <table id="datatable" class="table table-bordered table-striped align-middle dt-responsive nowrap w-100">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Title</th>
                                                    <th>Message</th>
                                                    <th>Type</th>
                                                    <th>Campuses</th>
                                                    <th>Workspaces</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($notifications as $notification)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $notification->title }}</td>
                                                        <td>Click view button to see the message</td>
                                                        <td>
                                                            @php
                                                                $type = strtolower($notification->type ?? 'unknown');
                                                                $color = $typeColors[$type] ?? 'badge-soft-secondary';
                                                            @endphp
                                                            <span class="badge badge-pill {{ $color }} font-size-11">{{ ucfirst($type) }}</span>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $campusNames = explode(',', $notification->campuses ?? '');
                                                                $campusIds = explode(',', $notification->campus_ids ?? '');
                                                            @endphp
                                                            @foreach($campusNames as $index => $name)
                                                                @php
                                                                    $color = $badgeColors[(int)$campusIds[$index] % count($badgeColors)] ?? 'badge-soft-secondary';
                                                                    
                                                                @endphp
                                                                <span class="badge badge-pill {{ $color }} font-size-11" title="Campus ID: {{ $campusIds[$index] }}">
                                                                    {{ $name }}
                                                                </span>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @php
                                                                $workspaceNames = explode(',', $notification->workspaces ?? '');
                                                                $workspaceIds = explode(',', $notification->workspace_ids ?? '');
                                                            @endphp
                                                            @foreach($workspaceNames as $index => $name)
                                                                @php
                                                                    $color = $badgeColors[$workspaceIds[$index] % count($badgeColors)] ?? 'badge-soft-secondary';
                                                                    
                                                                @endphp
                                                                <span class="badge badge-pill {{ $color }} font-size-11" title="Workspace ID: {{ $workspaceIds[$index] }}">
                                                                    {{ $name }}
                                                                </span>
                                                            @endforeach
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('d M Y h:i A') }}</td>
                                                        <td>
                                                            <a href="{{ route('staff.notifications.view', $notification->uid) }}" 
                                                                class="btn btn-sm btn-info btn-view-message">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Static Backdrop Modal -->
                                            
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
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
@endpush