@extends('modules.support.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Notifications details')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Notifications Details</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Notifications</a></li>
                        <li class="breadcrumb-item active">view</li>
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
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0 me-3">
                                    <img class="rounded-circle avatar-sm" src="{{ $photo }}" alt="{{ $notification->creator_firstname }} {{ $notification->creator_lastname }}">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mt-1">
                                        {{ $notification->creator_firstname }} {{ $notification->creator_lastname }}
                                    </h5>
                                    <small class="text-muted">{{ $notification->creator_email ?? 'email not found' }}</small>
                                </div>
                            </div>

                            <h4 class="font-size-16">{{ $notification->title }}</h4>

                            <p>{{ $notification->greeting ?? 'Dear User,' }}</p>

                            @foreach(explode("\n", $notification->message) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach

                            @if(!empty($notification->closing))
                                <p>{{ $notification->closing }}</p>
                            @endif

                            <hr/>

                            @php
                                $attachments = json_decode($notification->attachments ?? '[]', true);
                            @endphp

                            @if (!empty($attachments))
                                <div class="row">
                                    @foreach ($attachments as $attachment)
                                        <div class="col-xl-2 col-6">
                                            <div class="card">
                                                <img class="card-img-top img-fluid" src="{{ asset('storage/' . $attachment['path']) }}" alt="Attachment">
                                                <div class="py-2 text-center">
                                                    <a href="{{ asset('storage/' . $attachment['path']) }}" class="fw-medium" download>Download</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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
@endpush