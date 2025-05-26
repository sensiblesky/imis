@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'System Info & Status')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">System Info</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">System info</li>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <ul>
                                        <li><strong>PHP Version:</strong> {{ $phpVersion }}</li>
                                        <li><strong>Laravel Version:</strong> {{ $laravelVersion }}</li>
                                        <li><strong>App Environment:</strong> {{ $appEnv }}</li>
                                        <li><strong>Debug Mode:</strong> {{ $appDebug }}</li>
                                        <li><strong>App URL:</strong> {{ $appUrl }}</li>
                                        <li><strong>App Locale:</strong> {{ $appLocale }}</li>
                                        <li><strong>App Timezone:</strong> {{ $appTimezone }}</li>
                                        <li><strong>PHP max_execution_time:</strong> {{ $maxExecutionTime }} seconds</li>
                                        <li><strong>PHP memory_limit:</strong> {{ $memoryLimit }}</li>
                                        <li><strong>PHP post_max_size:</strong> {{ $postMaxSize }}</li>
                                        <li><strong>PHP upload_max_filesize:</strong> {{ $uploadMaxFilesize }}</li>
                                        <li><strong>Server OS & Kernel:</strong> {{ $serverOS }}</li>
                                        <li><strong>Database Connection:</strong> {{ $dbConnection }}</li>
                                        <li><strong>Database Host:</strong> {{ $dbHost }}</li>
                                        <li><strong>Database Name:</strong> {{ $dbName }}</li>
                                        <li><strong>Storage Usage (storage/app/public):</strong> {{ $storageUsageMB }} MB</li>
                                        <li><strong>Disk Free Space (storage):</strong> {{ $diskFreeGB }} GB</li>
                                        <li><strong>Current Server Time:</strong> {{ $currentTime }}</li>
                                    </ul>

                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- end card -->
                </div>
                <!-- end w-100 -->
            </div>
        </div>
    </div>

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
        document.getElementById('clear-log-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent normal form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "This will clear the entire log file!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form after confirmation
                    event.target.submit();
                }
            });
        });
    </script>

@endpush