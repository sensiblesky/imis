@extends('modules.administrator.components.layouts.app')
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
                <a href="#" class="btn btn-danger waves-effect waves-light me-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    <i class="mdi mdi-account-multiple-plus-outline label-icon"></i> Add Notification
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
                                                            <div class="btn-group" role="group">
                                                                <a href="#" 
                                                                    class="btn btn-sm btn-info btn-view-message" 
                                                                    data-title="{{ $notification->title }}" 
                                                                    data-message="{{ htmlentities($notification->message) }}">
                                                                    View
                                                                </a>
                                                                <button 
    class="btn btn-warning btn-sm edit-btn"
    data-bs-toggle="modal" 
    data-bs-target="#notificationModal"
    data-id="{{ $notification->id }}"
    data-title="{{ $notification->title }}"
    data-message="{{ htmlentities($notification->message) }}"
    data-type="{{ $notification->type }}"
    data-expires="{{ $notification->expires_at }}"
    data-campuses="{{ implode(',', $campusIds ?? []) }}"
    data-workspaces="{{ implode(',', $workspaceIds ?? []) }}"
>
    Edit
</button>




                                                                <form action="{{ route('admin.notifications.delete', $notification->uid) }}"
                                                                    method="POST"
                                                                    class="form-delete d-inline">
                                                                    @csrf
                                                                    @method('POST')
                                                                    <a href="#" class="btn btn-sm btn-danger btn-delete">Delete</a>
                                                                    <!-- <button type="button" class="btn btn-sm btn-danger btn-delete">Delete</button> -->
                                                                </form>
                                                            </div>
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
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="needs-validation" novalidate method="POST" action="{{ route('admin.notifications.store') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Create Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Notification Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                                <div class="invalid-feedback">Title is required.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="body" class="form-label">Notification Body</label>
                                <textarea class="form-control" id="body" name="body" rows="5" placeholder="Enter message" required></textarea>
                                <div class="invalid-feedback">Notification body is required.</div>
                            </div>
                        </div>

                        <!-- Campuses -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Campuses</label>
                                <div class="row">
                                    @foreach ($campuses as $campus)
                                        <div class="col-md-4">
                                            <div class="form-check form-checkbox-outline form-check-primary mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="campuses[]" 
                                                    id="campus_{{ $campus->id }}" 
                                                    value="{{ $campus->id }}">
                                                <label class="form-check-label" for="campus_{{ $campus->id }}">
                                                    {{ $campus->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Workspaces</label>
                                <div class="row">
                                    @foreach ($workspaces as $workspace)
                                        <div class="col-md-4">
                                            <div class="form-check form-checkbox-outline form-check-success mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="workspaces[]" 
                                                    id="workspace_{{ $workspace->id }}" 
                                                    value="{{ $workspace->id }}">
                                                <label class="form-check-label" for="workspace_{{ $workspace->id }}">
                                                    {{ $workspace->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Notification Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="" disabled selected>Select notification type</option>
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                    <option value="alert">Alert</option>
                                    <option value="notice">Notice</option>
                                </select>
                                <div class="invalid-feedback">Notification type is required.</div>
                            </div>
                        </div>

                        <!-- Expiration Date -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expires_at" class="form-label">Expiration Date (optional)</label>
                                <input type="date" class="form-control" id="expires_at" name="expires_at">
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                            <label class="form-check-label" for="termsCheck">
                                I confirm this notification is accurate.
                            </label>
                            <div class="invalid-feedback">You must confirm before submitting.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create Notification</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal for Editing Notification -->
                <form id="notificationForm" action="{{ route('admin.notifications.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="id" id="edit_notif_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notificationModalLabel">Edit Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_notif_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_notif_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_notif_message" class="form-label">Message</label>
                            <textarea class="form-control" id="edit_notif_message" name="message" rows="3" required></textarea>
                        </div>

                        <!-- Target Campuses -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Campuses</label>
                                <div class="row">
                                    @foreach ($campuses as $campus)
                                        <div class="col-md-4">
                                            <div class="form-check form-checkbox-outline form-check-primary mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="campuses[]" 
                                                    id="edit_campus_{{ $campus->id }}" 
                                                    value="{{ $campus->id }}">
                                                <label class="form-check-label" for="edit_campus_{{ $campus->id }}">
                                                    {{ $campus->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Workspaces</label>
                                <div class="row">
                                    @foreach ($workspaces as $workspace)
                                        <div class="col-md-4">
                                            <div class="form-check form-checkbox-outline form-check-success mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="workspaces[]" 
                                                    id="edit_workspace_{{ $workspace->id }}" 
                                                    value="{{ $workspace->id }}">
                                                <label class="form-check-label" for="edit_workspace_{{ $workspace->id }}">
                                                    {{ $workspace->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Notification Type -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit_notif_type" class="form-label">Notification Type</label>
                                <select class="form-select" id="edit_notif_type" name="type" required>
                                    <option value="" disabled selected>Select notification type</option>
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                    <option value="alert">Alert</option>
                                    <option value="notice">Notice</option>
                                </select>
                            </div>
                        </div>

                        <!-- Expiration Date -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_notif_expires" class="form-label">Expiration Date (optional)</label>
                                <input type="date" class="form-control" id="edit_notif_expires" name="expires_at">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>               
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
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('.form-delete');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-view-message').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const title = this.dataset.title;
                const message = this.dataset.message;

                Swal.fire({
                    title: title,
                    html: message.replace(/\n/g, '<br>'),
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            });
        });
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Event listener for all edit buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            // Reset all checkboxes
            document.querySelectorAll('input[name="campuses[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="workspaces[]"]').forEach(cb => cb.checked = false);

            // Populate campus checkboxes
            const campusIds = this.dataset.campuses?.split(',') || [];
            campusIds.forEach(id => {
                const cb = document.getElementById('edit_campus_' + id.trim());
                if (cb) cb.checked = true;
            });

            // Populate workspace checkboxes
            const workspaceIds = this.dataset.workspaces?.split(',') || [];
            workspaceIds.forEach(id => {
                const cb = document.getElementById('edit_workspace_' + id.trim());
                if (cb) cb.checked = true;
            });

            // Set other fields (title, message, etc.)
            document.getElementById('edit_notif_id').value = this.dataset.id;
            document.getElementById('edit_notif_title').value = this.dataset.title;
            document.getElementById('edit_notif_message').value = decodeHtml(this.dataset.message);
            document.getElementById('edit_notif_type').value = this.dataset.type;
            document.getElementById('edit_notif_expires').value = this.dataset.expires;
        });
    });

    // Helper to decode HTML entities in message
    function decodeHtml(html) {
        const txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }
});
</script>



@endpush