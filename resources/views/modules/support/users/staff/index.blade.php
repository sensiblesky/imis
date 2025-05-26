@extends('modules.support..components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'View Staff')

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
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex align-items-center">
                <img src="{{ $authUserPhoto }}" alt="" class="avatar-sm rounded">
                <div class="ms-3 flex-grow-1">
                    <h5 class="mb-2 card-title">Hello, {{ $authUserName }}</h5>
                    <p class="text-muted mb-0">
                        Welcome to the Staff Users Management page. Here you can view, add, and manage student users.
                    </p>
                </div>
                <div>
                <a href="{{ route('support.users.staff.create') }}" class="btn btn-danger waves-effect waves-light me-2">
                    <i class="mdi mdi-account-multiple-plus-outline label-icon"></i> Add Staff
                </a>
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl">Advanced Search</button>                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->
    
    <div class="row">
        <div class="col-lg-4">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Total Staff</p>
                            <h4 class="mb-0">{{ $filteredTotal }}</h4>
                        </div>
            
                        <div class="flex-shrink-0 align-self-center">
                            <div data-colors='["--bs-success", "--bs-transparent"]' dir="ltr" id="eathereum_sparkline_charts"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <p class="mb-0"> <span class="badge badge-soft-success me-1"><i class="bx bx-trending-up align-bottom me-1"></i> 18.89%</span> Increase last month</p>
                </div>
            </div>
        </div><!--end col-->
        <div class="col-lg-4">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Male Staff</p>
                            <h4 class="mb-0">{{ $filteredMale }}</h4>
                        </div>
        
                        <div class="flex-shrink-0 align-self-center">
                            <div data-colors='["--bs-success", "--bs-transparent"]' dir="ltr" id="new_application_charts"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <p class="mb-0"> <span class="badge badge-soft-success me-1"><i class="bx bx-trending-up align-bottom me-1"></i> 24.07%</span> Increase last month</p>
                </div>
            </div>
        </div><!--end col-->
        <div class="col-lg-4">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Female Staff</p>
                            <h4 class="mb-0">{{ $filteredFemale }}</h4>
                        </div>
        
                        <div class="flex-shrink-0 align-self-center">
                            <div data-colors='["--bs-success", "--bs-transparent"]' dir="ltr" id="total_approved_charts"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <p class="mb-0"> <span class="badge badge-soft-success me-1"><i class="bx bx-trending-up align-bottom me-1"></i> 8.41%</span> Increase last month</p>
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
                            <div class="row">
                                <div class="col-md-12">
                                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Gender</th>
                                            <th>Phone</th>
                                            <th>Campus</th>
                                            <th>Email</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                            <th>ID Printed</th>
                                            <th>Status</th>
                                            <th>Acton</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($staffUsers as $index => $staff)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $staff->title_name }}. {{ $staff->firstname }} {{ $staff->lastname }}</td>
                                                <td>{{ $staff->username }}</td>
                                                <td>
                                                    @php
                                                        $gender = strtolower($staff->gender);
                                                        $genderBadgeClass = match($gender) {
                                                            'm' => 'badge bg-primary',
                                                            'f' => 'badge bg-pink',
                                                            default => 'badge bg-secondary',
                                                        };
                                                    @endphp
                                                    <span class="{{ $genderBadgeClass }}">{{ strtoupper($gender) }}</span>

                                                        </td>
                                                <td>{{ $staff->phone }}</td>
                                            
                                                <td>
                                                    @php
                                                        $badgeColors = ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-pink', 'bg-secondary'];
                                                        $hash = crc32($staff->campus_name ?? 'unknown');
                                                        $index = $hash % count($badgeColors);
                                                        $badgeClass = $badgeColors[$index];
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ $staff->campus_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>{{ $staff->email }}</td>
                                                <td>{{ $staff->position_name }}</td>
                                                <td>{{ $staff->department_name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $staff->identity_print_status ? 'success' : 'secondary' }}">
                                                        {{ $staff->identity_print_status ? 'Printed' : 'Not Printed' }}
                                                    </span>
                                                </td>
                                                <td>
                                                @php
                                                    $status = strtolower($staff->status);
                                                    $badgeClass = match($status) {
                                                        'active' => 'badge bg-success',
                                                        'inactive' => 'badge bg-secondary',
                                                        'suspended' => 'badge bg-danger',
                                                        default => 'badge bg-warning',
                                                    };
                                                @endphp

                                                <span class="{{ $badgeClass }}">{{ ucfirst($staff->status) }}</span>

                                                </td>
                                                <td>
                                                <a href="{{ route('support.users.staff.view', $staff->staff_uid) }}" class="btn btn-primary waves-effect waves-light">
                                                    View
                                                </a>
                                                <form action="{{ route('support.users.staff.destroy', $staff->staff_uid) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger waves-effect waves-light delete-confirm">
                                                        Delete
                                                    </button>
                                                </form>

                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <!--  Extra Large modal example -->
                            <div class="modal fade bs-example-modal-xl" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myExtraLargeModalLabel">Search Staff</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form class="needs-validation" method="GET" action="{{ route('support.users.staff') }}" novalidate>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">First name</label>
                                                            <input type="text" class="form-control" name="firstname" value="{{ request('firstname') }}" placeholder="First name">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Last name</label>
                                                            <input type="text" class="form-control" name="lastname" value="{{ request('lastname') }}" placeholder="Last name">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Username</label>
                                                            <input type="text" class="form-control" name="username" value="{{ request('username') }}" placeholder="Username">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email" value="{{ request('email') }}" placeholder="Email">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Phone</label>
                                                            <input type="text" class="form-control" name="phone" value="{{ request('phone') }}" placeholder="Phone">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="status">
                                                                <option value="">-- Any --</option>
                                                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Title</label>
                                                            <select class="form-select" name="base_title_id">
                                                                <option value="">-- Any --</option>
                                                                @foreach ($titles as $title)
                                                                    <option value="{{ $title->id }}" {{ request('base_title_id') == $title->id ? 'selected' : '' }}>
                                                                        {{ $title->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Department</label>
                                                            <select class="form-select" name="base_department_id">
                                                                <option value="">-- Any --</option>
                                                                @foreach ($departments as $dept)
                                                                    <option value="{{ $dept->id }}" {{ request('base_department_id') == $dept->id ? 'selected' : '' }}>
                                                                        {{ $dept->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Position</label>
                                                            <select class="form-select" name="base_position_id">
                                                                <option value="">-- Any --</option>
                                                                @foreach ($positions as $position)
                                                                    <option value="{{ $position->id }}" {{ request('base_position_id') == $position->id ? 'selected' : '' }}>
                                                                        {{ $position->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-4">
                                                            <label class="form-label">Gender</label>
                                                            <select class="form-select" name="gender">
                                                                <option value="">-- Any --</option>
                                                                <option value="M" {{ request('gender') == 'M' ? 'selected' : '' }}>Male</option>
                                                                <option value="M" {{ request('gender') == 'F' ? 'selected' : '' }}>Female</option>
                                                                <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-4">
                                                            <label class="form-label">Campus</label>
                                                            <select class="form-select" name="campus_id">
                                                                <option value="">-- Any --</option>
                                                                @foreach ($campuses as $id => $name)
                                                                    <option value="{{ $id }}" {{ request('campus_id') == $id ? 'selected' : '' }}>
                                                                        {{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <button class="btn btn-primary" type="submit">Search</button>
                                                        <a href="{{ route('support.users.staff') }}" class="btn btn-secondary">Reset</a>
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
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-confirm').forEach(function(button) {
            button.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent form from submitting right away

                let form = this.closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to delete this staff member.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush