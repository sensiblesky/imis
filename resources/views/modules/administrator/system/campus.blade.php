@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Dashboard')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">System Settings</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">System</a></li>
                        <li class="breadcrumb-item active">Settings</li>
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
                                            <h5>Campus Settings</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-9 col-sm-6">
                                        <div class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                            <button type="button" class="btn btn-danger waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#campusModal" onclick="openCampusModal()"><i class="mdi mdi-bank-plus label-icon "></i> Create Campus</button>
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
                                                    <th>Campus Name</th>
                                                    <th>Campus Code</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($campuses as $index => $campus)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $campus->name }}</td>
                                                        <td>{{ $campus->campus_code }}</td>
                                                        <td>
                                                            @if ($campus->status == 1)
                                                                <span class="badge bg-success">Active</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                        <button 
                                                                type="button"
                                                                class="btn btn-sm btn-primary edit-campus-btn"
                                                                data-uid="{{ $campus->uid }}"
                                                                data-name="{{ $campus->name }}"
                                                                data-campus_code="{{ $campus->campus_code }}"
                                                                data-status="{{ $campus->status }}"
                                                                data-update-url="{{ route('admin.campus.update', $campus->uid) }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editcampusModal"
                                                            >
                                                                <i class="mdi mdi-pencil"></i> Edit
                                                        </button>

                                                        <form action="{{ route('admin.campus.destroy', $campus->uid) }}" method="POST" class="d-inline delete-form">
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
                                        <!-- Campus Modal -->
                                        <div class="modal fade" id="campusModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"  role="dialog" aria-labelledby="campusModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form id="campusForm" method="POST" action="{{ route('admin.campus.store') }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="campusModalLabel">Create Campus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <!-- Name -->
                                                            <div class="mb-3">
                                                                <label for="campusName" class="form-label">Campus Name</label>
                                                                <input type="text" class="form-control" id="campusName" name="name" required>
                                                            </div>

                                                            <!-- Status -->
                                                            <div class="mb-3">
                                                                <label for="campus_code" class="form-label">Campus Code</label>
                                                                <input type="text" class="form-control" id="campus_code" name="campus_code" required>
                                                            </div>

                                                            <!-- Status -->
                                                            <div class="mb-3">
                                                                <label for="campusStatus" class="form-label">Status</label>
                                                                <select class="form-select" id="campusStatus" name="status" required>
                                                                    <option value="1">Active</option>
                                                                    <option value="0">Inactive</option>
                                                                </select>
                                                            </div>


                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary" id="campusSubmitBtn">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- Campus Modal -->
                                        <!-- Modal -->
                                        <div class="modal fade" id="editcampusModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="campusModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                <form id="editCampusForm" method="POST"> <!-- ID changed -->
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editCampusModalLabel">Edit Campus</h5> <!-- ID changed -->
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <input type="hidden" id="editCampusUid" name="uid"> <!-- ID changed -->

                                                        

                                                        <div class="mb-3">
                                                            <label for="editCampusName" class="form-label">Campus Name</label>
                                                            <input type="text" class="form-control" id="editCampusName" name="name" required> <!-- ID changed -->
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="editCampusCode" class="form-label">Campus Code</label>
                                                            <input type="text" class="form-control" id="editCampusCode" name="campus_code" required> <!-- ID changed -->
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="editCampusStatus" class="form-label">Status</label>
                                                            <select class="form-select" id="editCampusStatus" name="status" required> <!-- ID changed -->
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary" id="editCampusSubmitBtn">Update</button> <!-- ID changed -->
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
        const campusForm = document.getElementById('editCampusForm');
        const modalTitle = document.getElementById('editCampusModalLabel');
        const submitBtn = document.getElementById('editCampusSubmitBtn');

        document.querySelectorAll('.edit-campus-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Get the data attributes
                const updateUrl = this.dataset.updateUrl;
                const uid = this.dataset.uid;
                const name = this.dataset.name;
                const status = this.dataset.status;
                const campus_code = this.dataset.campus_code;

                // Fill form fields
                document.getElementById('editCampusName').value = name;
                document.getElementById('editCampusCode').value = campus_code;
                document.getElementById('editCampusStatus').value = status;
                document.getElementById('editCampusUid').value = uid;

                // Update form action URL
                campusForm.action = updateUrl;

                // Update modal title and button
                modalTitle.textContent = 'Edit Campus';
                submitBtn.textContent = 'Update';
            });
        });

        // Reset form when modal is hidden
        const editCampusModal = document.getElementById('editcampusModal');
        editCampusModal.addEventListener('hidden.bs.modal', function () {
            campusForm.reset();
            campusForm.action = "{{ route('admin.campus.store') }}";
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