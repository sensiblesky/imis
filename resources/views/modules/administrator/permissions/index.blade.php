@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Permissions')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Permission List</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Permissions</a></li>
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
                                            <h5>Manage Permissions</h5>
                                        </div>
                                    </div>
                                    <p class="text-danger font-size-13 mb-0">Creating, editing, and deleting permissions has been restricted to prevent system errors. Please contact the administrator for any changes.</p>

                                </div>
                            </div>

                            <div>
                                




                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table  id="datatable" class="table table-bordered table-striped align-middle dt-responsive  nowrap w-100">

                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>PID</th>
                                                        <th>Workspace</th>
                                                        <th>Group</th>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th>Description</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($permissions as $index => $permission)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $permission->id }}</td>
                                                            <td>{{ $permission->workspace_name ?? '-' }}</td>
                                                            <td>{{ $permission->group_name ?? '-' }}</td>
                                                            <td>{{ $permission->name }}</td>
                                                            <td><code>{{ $permission->slug }}</code></td>
                                                            <td>{{ $permission->description }}</td>
                                                            <td>
                                                                @if($permission->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($permission->created_at)->format('Y-m-d') }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center">No permissions found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                               

                                </div>
                                <!-- end row -->
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