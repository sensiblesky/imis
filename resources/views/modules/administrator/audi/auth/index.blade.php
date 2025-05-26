@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Auth Logs')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Auth Logs</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Auth Logs</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex align-items-center">
                <img src="{{ $authUserPhoto }}" alt="" class="avatar-sm rounded">
                <div class="ms-3 flex-grow-1">
                    <h5 class="mb-2 card-title">Hello, {{ $authUserName }}</h5>
                    <p class="text-muted mb-0">
                        Here you can manage system authentication logs. You can view the logs of all users and filter them based on various criteria.
                    </p>
                </div>
                <div>
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl">Advanced Search</button>                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->


    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>IP Address</th>
                                            <th>Browser</th>
                                            <th>Platform</th>
                                            <th>City</th>
                                            <th>Country</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $index => $log)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $log->username }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($log->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $log->ip_address }}</td>
                                                <td>{{ $log->browser }}</td>
                                                <td>{{ $log->platform }}</td>
                                                <td>{{ $log->city }}</td>
                                                <td>{{ $log->country }}</td>
                                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.logs.authentication.view', $log->uid) }}" class="btn btn-primary btn-sm">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            
                                        @endforelse
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <!--  Extra Large modal example -->
                            <div class="modal fade bs-example-modal-xl" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myExtraLargeModalLabel">Search Logs</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form class="needs-validation" method="GET" action="{{ route('admin.logs.authentication') }}" novalidate>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Username</label>
                                                            <input type="text" class="form-control" name="username" value="{{ request('username') }}" placeholder="Username">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="status">
                                                                <option value="">-- Any --</option>
                                                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Action</label>
                                                            <select class="form-select" name="action">
                                                                <option value="">-- Any --</option>
                                                                <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                                                                <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                                                                <!-- Add more actions if needed -->
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">IP Address</label>
                                                            <input type="text" class="form-control" name="ip_address" value="{{ request('ip_address') }}" placeholder="IP Address">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">User Agent</label>
                                                            <input type="text" class="form-control" name="user_agent" value="{{ request('user_agent') }}" placeholder="User Agent">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Browser</label>
                                                            <select class="form-select" name="browser">
                                                                <option value="">-- Any --</option>
                                                                <option value="Chrome" {{ request('browser') == 'Chrome' ? 'selected' : '' }}>Chrome</option>
                                                                <option value="Firefox" {{ request('browser') == 'Firefox' ? 'selected' : '' }}>Firefox</option>
                                                                <option value="Safari" {{ request('browser') == 'Safari' ? 'selected' : '' }}>Safari</option>
                                                                <option value="Edge" {{ request('browser') == 'Edge' ? 'selected' : '' }}>Edge</option>
                                                                <option value="Opera" {{ request('browser') == 'Opera' ? 'selected' : '' }}>Opera</option>
                                                                <!-- Add more browsers as needed -->
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Platform</label>
                                                            <select class="form-select" name="platform">
                                                                <option value="">-- Any --</option>
                                                                <option value="Windows" {{ request('platform') == 'Windows' ? 'selected' : '' }}>Windows</option>
                                                                <option value="macOS" {{ request('platform') == 'macOS' ? 'selected' : '' }}>macOS</option>
                                                                <option value="Linux" {{ request('platform') == 'Linux' ? 'selected' : '' }}>Linux</option>
                                                                <option value="Android" {{ request('platform') == 'Android' ? 'selected' : '' }}>Android</option>
                                                                <option value="iOS" {{ request('platform') == 'iOS' ? 'selected' : '' }}>iOS</option>
                                                                <!-- Add more platforms if needed -->
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Device Type</label>
                                                            <select class="form-select" name="device_type">
                                                                <option value="">-- Any --</option>
                                                                <option value="Desktop" {{ request('device_type') == 'Desktop' ? 'selected' : '' }}>Desktop</option>
                                                                <option value="Mobile" {{ request('device_type') == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                                                <option value="Tablet" {{ request('device_type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                                                <option value="Bot" {{ request('device_type') == 'Bot' ? 'selected' : '' }}>Bot</option>
                                                                <!-- Add more device types if needed -->
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-6">
                                                            <label class="form-label">Start Date</label>
                                                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-6">
                                                            <label class="form-label">End Date</label>
                                                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                                        </div>
                                                    </div><br>



                                                    <div>
                                                        <button class="btn btn-primary" type="submit">Search</button>
                                                        <a href="{{ route('admin.logs.authentication') }}" class="btn btn-secondary">Reset</a>
                                                    </div>
                                                </form>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                            

                            </div>
                            <!-- end row -->
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
@endpush