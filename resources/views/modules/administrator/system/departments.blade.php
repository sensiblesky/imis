@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Departments')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Departments Settings</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">System</a></li>
                        <li class="breadcrumb-item active">Departments</li>
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
                
            @include('modules.administrator.components.layouts.partials.innerleftsidebar')

                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="row mb-3">
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="mt-2">
                                            <h5>Departments Settings</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-9 col-sm-6">
                                        <div class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                            <button type="button" class="btn btn-danger waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#departmentModalLabel" onclick="openCampusModal()"><i class="mdi mdi-bank-plus label-icon "></i> Create Department</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Department Name</th>
                                                    <th>Department Description</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($departments as $index => $department)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $department->name }}</td>
                                                        <td>{{ $department->description }}</td>
                                                        <td>
                                                            @if ($department->status == 1)
                                                                <span class="badge bg-success">Active</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                        <button 
                                                                type="button"
                                                                class="btn btn-sm btn-primary edit-department-btn"
                                                                data-uid="{{ $department->uid }}"
                                                                data-name="{{ $department->name }}"
                                                                data-department_description="{{ $department->description }}"
                                                                data-status="{{ $department->status }}"
                                                                data-update-url="{{ route('admin.department.update', $department->uid) }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editdepartmentModal"
                                                            >
                                                                <i class="mdi mdi-pencil"></i> Edit
                                                        </button>

                                                        <form action="{{ route('admin.department.destroy', $department->uid) }}" method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                        </form>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- department Modal -->
                                        <div class="modal fade" id="departmentModalLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"  role="dialog" aria-labelledby="departmentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form id="departmentForm" method="POST" action="{{ route('admin.department.store') }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="departmentModalLabel">Create Department</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <!-- Name -->
                                                            <div class="mb-3">
                                                                <label for="departmentName" class="form-label">Department Name</label>
                                                                <input type="text" class="form-control" id="departmentName" name="name" required>
                                                            </div>

                                                            <!-- Status -->
                                                            <div class="mb-3">
                                                                <label for="department_description" class="form-label">Description</label>
                                                                <input type="text" class="form-control" id="department_description" name="department_description" required>
                                                            </div>

                                                            <!-- Status -->
                                                            <div class="mb-3">
                                                                <label for="departmentStatus" class="form-label">Status</label>
                                                                <select class="form-select" id="departmentStatus" name="status" required>
                                                                    <option value="1">Active</option>
                                                                    <option value="0">Inactive</option>
                                                                </select>
                                                            </div>


                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary" id="departmentSubmitBtn">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- Campus Modal -->
                                        <!-- Modal -->
                                        <div class="modal fade" id="editdepartmentModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="departmentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                <form id="editDepartmentForm" method="POST" action="{{ route('admin.department.update', $department->uid) }}"> <!-- ID changed -->
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editdepartmentModalLabel">Edit Department</h5> <!-- ID changed -->
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <input type="hidden" id="editDepartmentUid" name="uid"> <!-- ID changed -->

                                                        

                                                        <div class="mb-3">
                                                            <label for="editDepartmentName" class="form-label">Campus Name</label>
                                                            <input type="text" class="form-control" id="editDepartmentName" name="name" required> <!-- ID changed -->
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="editDepartmentCode" class="form-label">Campus Code</label>
                                                            <input type="text" class="form-control" id="editDepartmentCode" name="department_description" required> <!-- ID changed -->
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="editDepartmentStatus" class="form-label">Status</label>
                                                            <select class="form-select" id="editDepartmentStatus" name="status" required> <!-- ID changed -->
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary" id="editDepartmentSubmitBtn">Update</button> <!-- ID changed -->
                                                    </div>
                                                </form>
                                                </div>
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

        @include('modules.administrator.components.layouts.partials.innerrightsidebar')
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var options = {
                chart: {
                    height: 250,
                    type: 'radialBar',
                    sparkline: {
                        enabled: true
                    }
                },
                series: [{{ $diskUsagePercentage }}],
                colors: ['#556ee6'],
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        track: {
                            background: '#e7e7e7',
                            strokeWidth: '97%',
                            margin: 5,
                        },
                        dataLabels: {
                            name: {
                                show: false
                            },
                            value: {
                                offsetY: 5,
                                fontSize: '22px',
                                formatter: function (val) {
                                    return val + "%";
                                }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        shadeIntensity: 0.15,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 65, 91]
                    },
                },
                stroke: {
                    lineCap: 'round'
                },
                labels: ['Used'],
            };

            var chart = new ApexCharts(document.querySelector("#myradial-chart"), options);
            chart.render();
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const departmentForm = document.getElementById('editDepartmentForm');
        const modalTitle = document.getElementById('editdepartmentModalLabel');
        const submitBtn = document.getElementById('editDepartmentSubmitBtn');

        document.querySelectorAll('.edit-department-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Get the data attributes
                const updateUrl = this.dataset.updateUrl;
                const uid = this.dataset.uid;
                const name = this.dataset.name;
                const status = this.dataset.status;
                const department_description = this.dataset.department_description;

                // Fill form fields
                document.getElementById('editDepartmentName').value = name;
                document.getElementById('editDepartmentCode').value = department_description;
                document.getElementById('editDepartmentStatus').value = status;
                document.getElementById('editDepartmentUid').value = uid;

                // Update form action URL
                departmentForm.action = updateUrl;

                // Update modal title and button
                modalTitle.textContent = 'Edit Department';
                submitBtn.textContent = 'Update';
            });
        });

        // Reset form when modal is hidden
        const editCampusModal = document.getElementById('editdepartmentModal');
        editCampusModal.addEventListener('hidden.bs.modal', function () {
            departmentForm.reset();
            departmentForm.action = "{{ route('admin.department.store') }}";
            modalTitle.textContent = 'Create Campus';
            submitBtn.textContent = 'Save';
        });
    });
</script>

<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>


@endpush