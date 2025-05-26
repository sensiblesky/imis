@extends('modules.administrator.components.layouts.app')

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpush 

@section('title', 'Profile')

@section('content')
<div class="row">
    <div class="col-xl-4">
        <div class="card overflow-hidden">
            <div class="bg-primary-subtle">
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-3">
                            <h5 class="text-primary">Welcome Back !</h5>
                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="avatar-md profile-user-wid mb-4">
                        </div>
                        
                    </div>

                    <div class="col-sm-8">
                        <div class="pt-4">
                            
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="font-size-15">Workspace</h5>
                                </div>
                               
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary waves-effect waves-light btn-sm">Edit Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end card -->

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Personal Information</h4>

                <div class="table-responsive">
                    
                </div>
            </div>
        </div>
        <!-- end card -->
    </div>         
    
    <div class="col-xl-8">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Session</h4>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>{{ $session['id'] }}</td>
                                <td>{{ $session['ip_address'] }}</td>
                                <td>{{ Str::limit($session['user_agent'], 80) }}</td>
                                <td>{{ $session['last_activity'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No active sessions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>


                    <!-- Fullscreen modal -->
                    <div id="exampleModalFullscreen" class="modal fade" tabindex="-1" aria-labelledby="#exampleModalFullscreenLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalFullscreenLabel">Login Location</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="google-map" style="height: 80vh; width: 100%;"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end modal -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
@endpush