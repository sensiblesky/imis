@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css">
@endpush 
@section('title', 'Backups')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Backup Management</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sytem</a></li>
                        <li class="breadcrumb-item active">Backup</li>
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
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif




    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex align-items-center">
                <img src="{{ $authUserPhoto }}" alt="" class="avatar-sm rounded">
                <div class="ms-3 flex-grow-1">
                    <h5 class="mb-2 card-title">Hello, {{ $authUserName }}</h5>
                    <p class="text-muted mb-0">
                        Welcome to the backup management. Here you can manage all your backups and restore them if needed.
                    </p>
                </div>
                <div>
                <a href="{{ route('admin.backup.download') }}" class="btn btn-danger waves-effect waves-light me-2">
                    <i class="mdi mdi mdi-cloud-lock-outline label-icon"></i> Backup Now
                </a>
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Auto Backup Settings</button>                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->


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
                                            <h5>Manage Backups</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table  id="datatable" class="table table-bordered table-striped align-middle dt-responsive  nowrap w-100">
                                               <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Type</th>
                                                        <th>Disk</th>
                                                        <th>Status</th>
                                                        <th>Status Message</th>
                                                        <th>Created By</th>
                                                        <th>Created At</th>
                                                        <th>Download Backup</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($logs as $log)
                                                        <tr>
                                                            <td>{{ $log->id }}</td>
                                                            <td>{{ ucfirst($log->type) }}</td>
                                                            <td>{{ $log->disk }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                                                    {{ ucfirst($log->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $log->message }}</td>
                                                            <td>{{ $log->firstname }} {{ $log->lastname }}</td>
                                                            <td>{{ $log->created_at }}</td>
                                                            <td>
                                                                <a href="../../../../../../storage/{{ $log->file_path }}" class="btn btn-primary btn-sm">
                                                                    <i class="mdi mdi-download"></i> Download
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        
                                                    @endforelse
                                                </tbody> 
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Static Backdrop Modal -->
                                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                           
                                                <form action="{{ route('admin.backup.settings') }}" method="POST">
                                                     <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">Backup Settings</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @csrf

                                                        <div class="mb-3">
                                                            <label for="storage" class="form-label">Select Storage</label>
                                                            <div class="alert alert-danger" role="alert">
                                                                Note: S3 backups will not work unless you provide valid S3 credentials and configure them properly in the <code>config/filesystems.php</code> file.
                                                            </div>
                                                            <select class="form-select" id="storage" name="disk" required>
                                                                <option value="">Select Storage</option>
                                                                <option value="local" {{ old('disk', $backupSettings->storage ?? '') == 'local' ? 'selected' : '' }}>Local</option>
                                                                <option value="s3" {{ old('disk', $backupSettings->storage ?? '') == 's3' ? 'selected' : '' }}>S3</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="frequency" class="form-label">Backup Frequency</label>
                                                            <select class="form-select" id="frequency" name="frequency" required>
                                                                <option value="">Select Frequency</option>
                                                                <option value="daily" {{ old('frequency', $backupSettings->frequency ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                                                <option value="weekly" {{ old('frequency', $backupSettings->frequency ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                                                <option value="monthly" {{ old('frequency', $backupSettings->frequency ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="time" class="form-label">Backup Time (HH:MM 24hr)</label>
                                                            <div class="input-group" id="timepicker-input-group2">
                                                                <input type="time" class="form-control" id="time" name="time" required value="{{ old('time', $backupSettings->time ?? '02:00') }}">
                                                                <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="type" class="form-label">Backup Type</label>
                                                            <select class="form-select" id="type" name="type" required>
                                                                <option value="">Select Type</option>
                                                                <option value="full" {{ old('type', $backupSettings->type ?? '') == 'full' ? 'selected' : '' }}>Full (DB + Files)</option>
                                                                <option value="database" {{ old('type', $backupSettings->type ?? '') == 'database' ? 'selected' : '' }}>Database Only</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="runing_status" class="form-label">Status</label>
                                                            <select class="form-select" id="runing_status" name="status" required>
                                                                <option value="">Select Status</option>
                                                                <option value="1" {{ old('status', $backupSettings->enabled ?? 1) == 1 ? 'selected' : '' }}>Enabled</option>
                                                                <option value="0" {{ old('status', $backupSettings->enabled ?? 1) == 0 ? 'selected' : '' }}>Disabled</option>
                                                            </select>
                                                        </div>

                                                        

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Settings</button>
                                                    </div>
                                                </form>

                                            
                                        </div>
                                    </div>
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
    <!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
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