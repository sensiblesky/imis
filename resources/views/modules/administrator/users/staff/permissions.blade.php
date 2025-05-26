@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap4-duallistbox@4.0.2/dist/bootstrap-duallistbox.min.css">


@endpush 
@section('title', 'Dashboard')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Assign / Update Staff Permissions</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Users</a></li>
                        <li class="breadcrumb-item">Staff</li>
                        <li class="breadcrumb-item active">permissions</li>
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
                                    <div class="col-xl-6 col-sm-6">
                                        <div class="mt-2">
                                            <h5>Assign Permissions to {{ $staff->firstname }} {{ $staff->lastname }} | {{ $staff->username }} </h5>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <div>


                          



                            <form method="POST" action="{{ route('admin.users.staff.update.permissions', $staff->uid) }}" id="permissionsForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <select multiple="multiple" size="15" name="permissions[]" id="dual_permissions">
                                            @foreach($workspaces as $workspace)
                                                <optgroup label="{{ $workspace->display_name }}">
                                                    @if(isset($permissionsByWorkspace[$workspace->id]))
                                                        @foreach($permissionsByWorkspace[$workspace->id] as $permission)
                                                            <option value="{{ $permission->id }}"
                                                                {{ in_array($permission->id, $userPermissionIds) ? 'selected' : '' }}>
                                                                {{ $permission->group_name }} - {{ $permission->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <br>

                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="bx bx-check-double font-size-16 align-end me-2"></i> Save Permissions
                                </button>
                            </form>









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
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap4-duallistbox@4.0.2/dist/jquery.bootstrap-duallistbox.min.js"></script>
<script>
$(document).ready(function() {
    var dualListbox = $('#dual_permissions').bootstrapDualListbox({
        nonSelectedListLabel: 'Available Permissions',
        selectedListLabel: 'Assigned Permissions',
        preserveSelectionOnMove: 'moved',
        moveOnSelect: true, // ✅ Important change
        filterPlaceHolder: 'Search...',
        infoText: 'Showing all {0}',
        infoTextFiltered: '<span class="badge badge-warning">Filtered</span> {0} from {1}',
        infoTextEmpty: 'No permissions available'
    });

    // On submit
    $('#permissionsForm').on('submit', function(e) {
        // First, unselect everything
        $('#dual_permissions option').prop('selected', false);

        // Then, find assigned options (right side list)
        var container = $('#dual_permissions').bootstrapDualListbox('getContainer');
        var assignedOptions = container.find('.box2 select option');

        assignedOptions.each(function() {
            var value = $(this).val();
            $('#dual_permissions option[value="' + value + '"]').prop('selected', true);
        });
    });
});
</script>


@endpush