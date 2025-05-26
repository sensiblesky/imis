@extends('modules.staff.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Exam Centers')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Exam Centers Settings</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Exam Centers</a></li>
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
                       Here you can manage your exam centers settings
                    </p>
                </div>
                <a href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="btn btn-danger waves-effect waves-light me-2">
                    <i class="mdi mdi-account-multiple-plus-outline label-icon"></i> Create Application Window
                </a>
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
                                        <h5>Manage Exam Centers</h5>
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

                                            $statusColors = [
                                                'active' => 'badge-soft-success',
                                                'inactive' => 'badge-soft-danger',
                                                'pending' => 'badge-soft-warning',
                                            ];
                                        @endphp

                                        <table id="datatable" class="table table-bordered table-striped align-middle dt-responsive nowrap w-100">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Campus</th>
                                                    <th>Semester</th>
                                                    <th>Academic Year</th>
                                                    <th>Planned Students</th>
                                                    <th>Enrolled Students</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($settings as $setting)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            @php
                                                                $campusColor = $badgeColors[$setting->campus_id % count($badgeColors)] ?? 'badge-soft-secondary';
                                                            @endphp
                                                            <span class="badge badge-pill {{ $campusColor }}">{{ $setting->campus_name }}</span>
                                                        </td>
                                                        <td>{{ $setting->semester_name }}</td>
                                                        <td>{{ $setting->academic_year_name }}</td>
                                                        <td><span class="badge-soft-success">{{ $setting->planned_students }}</span></td>
                                                        <td><span class="badge-soft-danger">{{ $setting->enrolled_students }}</span></td>
                                                        <td>
                                                            @php
                                                                $status = strtolower($setting->status ?? 'unknown');
                                                                $color = $statusColors[$status] ?? 'badge-soft-secondary';
                                                            @endphp
                                                            <span class="badge badge-pill {{ $color }}">{{ ucfirst($status) }}</span>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($setting->created_at)->format('d M Y h:i A') }}</td>
                                                        <td>
                                                            <a href="{{ route('staff.exam-centers.settings.view', $setting->uid) }}" class="btn btn-sm btn-info">
                                                                View
                                                            </a>

                                                            {{-- Add other actions like edit/delete if needed --}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>



                            <!-- Static Backdrop Modal -->
                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('staff.exam-centers.settings.store') }}" method="POST" class="needs-validation" novalidate>
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Application Window</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="entries-container">
                                                    <!-- Dynamic entries will be injected here -->
                                                </div>

                                                <button type="button" class="btn btn-outline-primary mt-2" id="add-entry-btn">Add Another</button>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
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
        // Bootstrap validation enforcement
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>


<script>
    const campuses = @json($campuses);
    const semesters = @json($semesters);
    const academicYears = @json($academicYears);

    let selectedCampuses = [];
    let entryCount = 0;

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('entries-container');
        const addBtn = document.getElementById('add-entry-btn');

        addBtn.addEventListener('click', function () {
            // Validate last group before adding new
            const lastEntry = container.querySelector('.entry-group:last-child');
            if (lastEntry && !validateEntryGroup(lastEntry)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Entry',
                    text: 'Please complete the current entry before adding another.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            const remainingCampuses = Object.entries(campuses)
                .filter(([id]) => !selectedCampuses.includes(id));

            if (remainingCampuses.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Incomplete Entry',
                    text: 'Please complete the current entry before adding another.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            const campusOptions = remainingCampuses.map(([id, name]) => `<option value="${id}">${name}</option>`).join('');

            const semesterRadios = Object.entries(semesters).map(([id, name]) => `
                <div class="form-check form-radio-primary mb-2">
                    <input class="form-check-input" type="radio" name="entries[${entryCount}][semester_id]" id="semester_${entryCount}_${id}" value="${id}" required>
                    <label class="form-check-label" for="semester_${entryCount}_${id}">${name}</label>
                </div>
            `).join('');

            const yearRadios = Object.entries(academicYears).map(([id, range]) => `
                <div class="form-check form-radio-primary mb-2">
                    <input class="form-check-input" type="radio" name="entries[${entryCount}][academic_year_id]" id="year_${entryCount}_${id}" value="${id}" required>
                    <label class="form-check-label" for="year_${entryCount}_${id}">${range}</label>
                </div>
            `).join('');

            const html = `
                <div class="entry-group border rounded p-3 mb-4 bg-light position-relative">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Campus</label>
                            <select class="form-select campus-select" name="entries[${entryCount}][campus_id]" required>
                                <option selected disabled value="">Choose campus...</option>
                                ${campusOptions}
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            ${semesterRadios}
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            ${yearRadios}
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">#No of Students</label>
                            <input type="number" class="form-control" name="entries[${entryCount}][number_of_students]" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="entries[${entryCount}][status]" required>
                                <option selected disabled value="">Choose status...</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Intake</label>
                            <select class="form-select" name="entries[${entryCount}][intake]" required>
                                <option selected disabled value="">Choose status...</option>
                                <option value="1">October Intake</option>
                                <option value="2">March Intake</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            entryCount++;

            if (remainingCampuses.length === 1) {
                addBtn.disabled = true;
            }
        });

        function validateEntryGroup(group) {
            const requiredFields = group.querySelectorAll('select, input[type="number"], input[type="radio"]:required');
            let valid = true;

            requiredFields.forEach(field => {
                if (field.type === "radio") {
                    const name = field.name;
                    const checked = group.querySelector(`input[name="${name}"]:checked`);
                    if (!checked) valid = false;
                } else if (!field.value) {
                    valid = false;
                }
            });

            if (valid) {
                const campusSelect = group.querySelector('.campus-select');
                const selectedCampus = campusSelect.value;
                if (selectedCampus && !selectedCampuses.includes(selectedCampus)) {
                    selectedCampuses.push(selectedCampus);
                }
            }

            return valid;
        }

        // Trigger first group load
        addBtn.click();
    });
</script>
@endpush