@extends('modules.support.components.layouts.app')

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        h10 {
            color: blue;
            /*line-height: ;*/
            font-size: 25px;
        }

        table {
            border: none;
            border-collapse: collapse;
            width: 756px;
            font-family: 'Source Sans Pro Black', sans-serif;
        }

        h12 {
            text-transform: uppercase; /* Convert text to uppercase */
            font-size: 25px;
            color: black;
        }

        #UserProfile{
            padding: 8%;
        }
        td, th {
            padding: 3px;
            text-align: left;
            line-height: 1; /* Decrease line height */
            /*font-weight: bold; !* Set font weight to bold *!*/
        }

        .center {
            text-align: center;
        }
        
        
        
        
#cardgenerator2 {
    background-image: url('../../../../../assets/images/templates/2/id_background_template.png'); /* Adjust the path */
    background-size: contain; /* Ensures the image is fully visible without cropping */
    background-position: center;
    background-repeat: no-repeat;
    width: 810px; /* Set to the exact width of the image */
    height: 500px; /* Set to the exact height of the image */
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white; /* Ensure text is visible */
    padding: 20px;
}

/* Ensure text is visible above background */
.overlay-text {
    position: relative;
    font-size: 20px;
    font-weight: bold;
    z-index: 2; /* Keeps text above the background */
}


    </style>
@endpush 

@section('title', 'Profile')

@section('content')
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

    @if($student->is_deleted)
        <div class="alert alert-danger">
            <strong>Alert!</strong> This user has been deleted on {{ $student->is_deleted_at  }}, Please if this is suspisous activity, take legal action as soon as possible.
        </div>
    @endif

<div class="row">
    <div class="col-xl-4">
        <div class="card overflow-hidden">
            <div class="bg-primary-subtle">
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-3">
                            <h5 class="text-primary">Welcome Back !</h5>
                            <p>{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</p>
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
                            <img src="{{ $student->photo_base64 }}" alt="Profile Photo" class="img-thumbnail rounded-circle">
                        </div>
                        <h5 class="font-size-15 text-truncate">{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</h5>
                        <p class="text-muted mb-0 text-truncate">{{ $student->username  }}</p>
                    </div>

                    <div class="col-sm-8">
                        <div class="pt-4">
                            
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="font-size-15">Workspace</h5>
                                    <p class="text-muted mb-0">Student Only</p>
                                </div>
                                <div class="col-6">
                                    <h5 class="font-size-15">Campus</h5>
                                        <span class="badge badge-soft-success font-size-12"> {{ $student->campus_name  }} </span>                         
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('support.users.student.edit', $student->uid) }}" class="btn btn-primary waves-effect waves-light btn-sm">Edit Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end card -->

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Student Information</h4>

                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Full Name :</th>
                                <td>{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Mobile :</th>
                                <td>{{ $student->phone }}</td>
                            </tr>
                            <tr>
                                <th scope="row">E-mail :</th>
                                <td>{{ $student->email }}</td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Academic Year</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $student->academic_year_range  }} </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Campus</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $student->campus_name  }} </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Student Intake :</th>
                                <td><span class="badge badge-soft-success font-size-12">{{ $student->intake_name  }}  </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Student Level :</th>
                                <td><span class="badge badge-soft-success font-size-12">{{ $student->level_name  }}  </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Student Course :</th>
                                <td><span class="badge badge-soft-success font-size-12">{{ $student->program_name  }}  </span></td>
                            </tr>
                            <tr>
                                <th scope="row">Valid Until</th>
                                <td><span class="badge badge-soft-success font-size-12"> {{ $student->valid_until_description  }} </span></td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
                
                <br>
                @if ($student->identity_card == 0)
                    <center>
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                Issue Student Identity (Late <= 2022)
                        </button><br><br>
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                            Issue Student Identity
                        </button>
                    </center>
                @else
                    <p class="text-danger">This student has already been issued an identity card. To issue a new one, please renew the ID in the system and make a payment of {{ $otherFeeAmount }} TZS.</p>
                    <center>
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop3">
                                Renew Student Identity
                        </button><br><br>
                    </center>
                @endif
            </div>
            
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Account Security</h4>

                <div class="col-lg-12">
                    <div class="card mini-stats-wid">
                            
                        

                        <div class="bg-light p-3 d-flex mb-3 rounded">
                            <img src="#" alt="" class="avatar-sm rounded me-3">
                            <div class="flex-grow-1">
                                <h5 class="font-size-15 mb-2"><a href="#" class="text-body">Two Factor Authentication</a> 
                                    <span class="badge badge-soft-info">{{ $student->two_factor_status ? 'Enabled' : 'Disabled' }}</span>
                                </h5>
                                <p class="mb-0 text-muted"><i class="bx bx-map text-body align-middle"></i> 
                                    @if ($student->two_factor_method === 'email')
                                        <a href="#">Email Two Factor Authentication</a>
                                    @elseif ($student->two_factor_method === 'google')
                                        <a href="#">Google Authenticator method</a>
                                    @else
                                        Not Set
                                    @endif
                                </p>
                            </div>
                            <div>
                                <div class="square-switch">
                                    <!-- Check if 2FA is enabled and set the toggle accordingly -->
                                    <input type="checkbox"
                                        id="square-switch1"
                                        switch="none"
                                        {{ $student->two_factor_status == 1 ? 'checked' : '' }}
                                        disabled
                                    />
                                    <label for="square-switch1"
                                        data-on-label="On"
                                        data-off-label="Off"
                                        onclick="handleToggleAttempt()">
                                    </label>


                                </div>
                            </div>
                        </div>



                        

                    </div>
                </div>
            </div>
        </div>
    </div>         
    
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li hidden class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#home-1" role="tab">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block">Print Student Identity</span> 
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-bs-toggle="tab" href="#home-2" role="tab">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block">Student Identity History</span> 
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-3 text-muted">
                    <div class="tab-pane" id="home-1" role="tabpanel">        
                        
                    </div>
                    <div class="tab-pane active" id="home-2" role="tabpanel">
                        @if(isset($allRequests) && $allRequests->count())
                            <table class="table table-bordered mt-4">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>Attachment</th>
                                        <th>Submitted At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allRequests as $index => $request)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ ucwords(str_replace('_', ' ', $request->replacement_reason)) }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($request->status == 'pending') bg-warning 
                                                    @elseif($request->status == 'approved') bg-success 
                                                    @elseif($request->status == 'rejected') bg-danger 
                                                    @endif">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $request->description }}</td>
                                            <td>
                                                @if($request->attachment_path)
                                                    <a href="{{ asset('storage/' . $request->attachment_path) }}" target="_blank">View</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($request->created_at)->toDayDateTimeString() }}</td>
                                            <td>
                                                @if($request->status == 'pending')
                                                    <button 
                                                        class="btn btn-success btn-sm btn-approve" 
                                                        data-id="{{ $request->id }}">
                                                        Approve
                                                    </button>

                                                    <button 
                                                        class="btn btn-danger btn-sm btn-reject" 
                                                        data-id="{{ $request->id }}">
                                                        Reject
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="mt-4">No replacement requests found.</p>
                        @endif
                    </div>
                    
                </div>

                <!-- Static Backdrop Modal -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="border-radius: 10px; width : 156%;">
                            <div class="modal-header">
                                <h5 class="modal-title text-danger" id="staticBackdropLabel">Outdated Template, use only for graduants</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="inbox-widget" id="cardgenerator">
                                                
                                    <!-- <img src="{{ asset('assets/images/templates/ids/1/studentidheader.png') }}" height="100%" width="100%"> -->
                                        <table bgcolor="white">
                                        <tr>
                                            <td colspan="4"><img src="{{ asset('assets/images/templates/ids/1/studentidheader.png') }}" height="100%" width="100%"></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="3"><center><img id="UserProfile" src="{{ $student->photo_base64 }}" alt="Student Photo" width="220px" height="220px"></center></td>
                                            <td><h10>Surname</h10> <br> <h12><b>{{ $student->lastname }}</b></h12></td>
                                            <td><h10>First Name</h10> <br> <h12><b>{{ $student->firstname }}</b></h12></td>
                                            <td><center><h10>Middle Name</h10> <br><h12><b>{{ $student->middlename }}</b></h12></center></td>
                                        </tr>
                                        <tr>
                                        <td colspan="2"><h10>Registration Number</h10> <br><h12><b>{{ $student->username }}</b></10></td>
                                            </b></h12></td>
                                            <td><center><h10>Gender</h10><br><h12><b>{{ $student->gender }}</b></h12></center></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><h10>Program</h10><br><h12><b>{{ $student->program_name  }}</b></h12></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan="2"><h10>Valid Until</h10><br><h12><b>{{ $student->valid_until_description  }}</b></h12><br><img style="width: 300px; height: 55px;" id="img" src="{{ $barcodeDataUri }}" alt="Bar Code"></td>
                                            <td>
                                                <img src="{{ $qrCodeDataUri }}" style="height: 120px; width: 120px;">
                                            </td>
                                        </tr>
                                        <tr style="height: 5px;">
                                            <td colspan="3"></td>
                                        </tr>
                                    </table>
                                    
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="downloadimage()">Print</button>
                            </div>
                        </div>
                    </div>
                </div>                 
                <!-- Static Backdrop Modal -->
                <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="border-radius: 10px; width : 170%;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Student Identity of : {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="inbox-widget" id="cardgenerator2">
                                    <table>
                                        <tr style="height: 120px;">
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="5"><center><img id="UserProfile" src="{{ $student->photo_base64 }}" alt="Student Photo" width="220px" height="220px"><br><img style="width: 170px; height: 55px;" id="img" src="{{ $barcodeDataUri }}" alt="Bar Code"></center></td>
                                            <td><h10>Surname</h10> <br> <h12><b>{{ $student->lastname }}</b></h12></td>
                                            <td><h10>First Name</h10> <br> <h12><b>{{ $student->firstname }}</b></h12></td>
                                            <td><center><h10>Middle Name</h10> <br><h12><b>{{ $student->middlename }}</b></h12></center></td>
                                        </tr>
                                        <tr>
                                        <td colspan="2"><h10>Registration Number</h10> <br><h12><b>{{ $student->username }}</b></10></td>



                                            </b></h12></td>
                                            <td><center><h10>Gender</h10><br><h12><b>{{ $student->gender }}</b></h12></center></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><h10>Program</h10><br><h12><b>{{ $student->program_name }}</b></h12></td>
                                        </tr>
                                        <tr>
                                            <td colspan=""><h10>Academic Year</h10><br><h12><b>2024/2025</b></h12><br></td>
                                            <td><h10>Semester</h10><br><h12><b><center>@if ($student->intake_name === "OCTOBER") II @else I @endif</b></h12></td>
                                            <td rowspan="2">
                                                <img src="{{ $qrCodeDataUri }}" style="height: 120px; width: 120px;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><h10>Valid Until</h10><br><h12><b>{{ $student->valid_until_description }}</b></h12><br></td>
                                            
                                        </tr>
                                    </table>
                                    <!-- <span class="badge bg-danger">Sorry you cannot issue this student Identity because student has not yet complete tution fee for semester II</span> -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="downloadimage2()">Print</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Static Backdrop Modal -->
                <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="border-radius: 10px; width : 170%;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"> Identity Replacement For : {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" action="{{ route('support.users.student.identity.renewal') }}" method="POST" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <input type="hidden" name="uid" value="{{ $student->uid }}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="attachment" class="form-label">Attach Document</label>
                                                <input type="file" class="form-control" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="invalid-feedback">
                                                    Please upload a valid attachment (PDF or image).
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="replacement_reason" class="form-label">Replacement Reason</label>
                                                <select class="form-select" id="replacement_reason" name="replacement_reason" required>
                                                    <option value="error_correction">Error Correction</option>
                                                    <option value="lost">lost</option>
                                                    <option value="broken">broken</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Reason for Replacement</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Explain why you need a new ID card..."></textarea>
                                        <div class="invalid-feedback">
                                            Please provide a reason for replacement.
                                        </div>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="termsCheck" required>
                                        <label class="form-check-label" for="termsCheck">
                                            I confirm the information is accurate.
                                        </label>
                                        <div class="invalid-feedback">
                                            You must confirm before submitting.
                                        </div>
                                    </div>

                                    <div>
                                        <button class="btn btn-primary" type="submit">Submit Request</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">LoggedIn Active Session</h4>
                <p class="text-muted mb-4">You are currently logged in from the following devices. If you don't recognize any of these devices, you can terminate the session.</p>
                <div class="table-responsive">
                    <table id="datatable2" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Last Activity</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($sessions as $index => $session)
                            <tr>
                                <td>{{ $index + 1 }}</td> {{-- Session # --}}
                                <td>{{ $session['ip_address'] }}</td>
                                <td>{{ $session['user_agent'] }}</td>
                                <td>{{ $session['last_activity'] }}</td>
                                <td>
                                <form method="POST" action="{{ route('support.users.student.terminateSession', $student->uid) }}" style="display:inline" class="terminate-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="session_id" value="{{ $session['id'] }}">
                                    <button type="submit" class="btn btn-sm btn-danger">Terminate</button>
                                </form>


                                    @if($session['most_recent'])
                                        <span class="badge bg-info ms-2">Most Recent</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No active sessions found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Login Activities</h4>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>Credentials</th>
                                <th>Status</th>
                                <th>Platform</th>
                                <th>Browser</th>
                                <th>Location</th>
                                <th>ISP</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loginLogs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>
                                        Username: {{ $log->username }}<br>
                                        Password: {{ $log->password }}<br>    
                                    <br>
                                    <td>
                                        <span class="badge badge-soft-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->platform }}</td>
                                    <td>{{ $log->browser }} {{ $log->browser }}</td>
                                    <td>
                                        {{ $log->city }}, {{ $log->country }}<br>
                                        @if($log->latitude && $log->longitude)
                                            <span class="badge badge-soft-success">{{ $log->longitude }}</span>
                                            <span class="badge badge-soft-danger">{{ $log->latitude }}</span><br>

                                            <span class="badge badge-soft-danger">
                                                <a href="#" onclick="showGoogleMap({{ $log->latitude }}, {{ $log->longitude }})" class="text-decoration-none">Track Location</a>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $log->asy }} </td>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty
                                
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

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">General Activities</h4>
                <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100"">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>Action</th>
                                <th>Changes</th>
                                <th>Platform</th>
                                <th>Browser</th>
                                <th>Location</th>
                                <th>ISP</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($GeneralLogs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>
                                        {{ $log->action }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="showComparisonModal('{{ base64_encode($log->old_data) }}', '{{ base64_encode($log->new_data) }}')">
                                            View Changes
                                        </button>
                                    </td>
                                    <td>{{ $log->platform }}</td>
                                    <td>{{ $log->browser }} {{ $log->browser }}</td>
                                    <td>
                                        {{ $log->city }}, {{ $log->country }}<br>
                                        @if($log->latitude && $log->longitude)
                                            <span class="badge badge-soft-success">{{ $log->longitude }}</span>
                                            <span class="badge badge-soft-danger">{{ $log->latitude }}</span><br>
                                            <span class="badge badge-soft-danger">
                                                <a href="#" onclick="showGoogleMap({{ $log->latitude }}, {{ $log->longitude }})" class="text-decoration-none">Track Location</a>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $log->asy }} </td>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty

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
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <!-- jquery step -->
    <script src="{{ asset('assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>
    

    <!-- form wizard init -->
    <script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Approve button
    document.querySelectorAll('.btn-approve').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            Swal.fire({
                title: 'Are you sure?',
                text: "You are approving this request.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    sendAction(id, 'approve');
                }
            });
        });
    });

    // Reject button
    document.querySelectorAll('.btn-reject').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            Swal.fire({
                title: 'Reject Request',
                input: 'textarea',
                inputLabel: 'Reason for rejection',
                inputPlaceholder: 'Enter reason here...',
                inputAttributes: {
                    'aria-label': 'Enter reason here',
                },
                showCancelButton: true,
                confirmButtonText: 'Reject',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('Rejection reason is required');
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    sendAction(id, 'reject', result.value);
                }
            });
        });
    });

    function sendAction(id, action, reason = null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/support/users/students/approve-or-reject-renewal/${id}`;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;

        form.appendChild(csrf);
        form.appendChild(actionInput);

        if (reason) {
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejected_reason';
            reasonInput.value = reason;
            form.appendChild(reasonInput);
        }

        document.body.appendChild(form);
        form.submit();
    }
});
</script>


<script>
    function handleToggleAttempt() {
        Swal.fire({
            icon: 'warning',
            title: 'Action Not Allowed',
            text: 'Staff cannot enable or disable Two Factor Authentication for users. The user must configure it themselves.',
            confirmButtonText: 'Okay'
        });
    }
</script>
















<script src="https://cdn.jsdelivr.net/npm/dom-to-image-more@2.8.0/dist/dom-to-image-more.min.js"></script>
    <script type="text/javascript">
        function downloadimage2() {
            var element = document.getElementById("cardgenerator2");
            var staffId = "{{ $student->uid }}"; // Get the staff's id from Laravel
            var staffUsername = "{{ $student->username }}"; // Get the staff's username from Laravel

            // Make an AJAX request to update id_print
            $.ajax({
                url: '/support/users/students/update-identity-print-status/' + staffId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'success') {
                        
                        // Generate and download the image using dom-to-image-more
                        domtoimage.toPng(element, {
                            quality: 1, // Set quality to maximum
                            scale: 10 // Increase the scale for better quality
                        })

                            .then(function(dataUrl) {
                                // Create a canvas with specific dimensions
                                var canvas = document.createElement('canvas');
                                canvas.width = 1126; // Set width as needed
                                canvas.height = 740; // Set height as needed
                                var context = canvas.getContext('2d');

                                // Create new image element
                                var img = new Image();
                                img.onload = function() {
                                    // Draw the image onto the canvas with specified dimensions
                                    context.drawImage(img, 0, 0, canvas.width, canvas.height);

                                    // Convert canvas to data URL and trigger download
                                    var link = document.createElement("a");
                                    link.href = canvas.toDataURL('image/png');
                                    link.download = staffUsername + ".png"; // Include the staff's username in the filename
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);
                                };
                                img.src = dataUrl; // Set source to generated image data URL
                            })
                            .catch(function(error) {
                                console.error("Error generating image: ", error);
                            });
                    } else {
                        console.error("Error updating id_print: ", response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: ", status, error);
                }
            });
        }
    </script>
    
    
    
    <script type="text/javascript">
        function downloadimage() {
            var element = document.getElementById("cardgenerator");
            var staffId = "{{ $student->uid }}"; // Get the staff's id from Laravel
            var staffUsername = "{{ $student->username }}"; // Get the staff's username from Laravel

            // Make an AJAX request to update id_print
            $.ajax({
                url: '/support/users/students/update-identity-print-status/' + staffId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Generate and download the image using dom-to-image-more
                        domtoimage.toPng(element, {
                            quality: 1, // Set quality to maximum
                            scale: 10 // Increase the scale for better quality
                        })

                            .then(function(dataUrl) {
                                // Create a canvas with specific dimensions
                                var canvas = document.createElement('canvas');
                                canvas.width = 1126; // Set width as needed
                                canvas.height = 740; // Set height as needed
                                var context = canvas.getContext('2d');

                                // Create new image element
                                var img = new Image();
                                img.onload = function() {
                                    // Draw the image onto the canvas with specified dimensions
                                    context.drawImage(img, 0, 0, canvas.width, canvas.height);

                                    // Convert canvas to data URL and trigger download
                                    var link = document.createElement("a");
                                    link.href = canvas.toDataURL('image/png');
                                    link.download = staffUsername + ".png"; // Include the staff's username in the filename
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);
                                };
                                img.src = dataUrl; // Set source to generated image data URL
                            })
                            .catch(function(error) {
                                console.error("Error generating image: ", error);
                            });
                    } else {
                        console.error("Error updating id_print: ", response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: ", status, error);
                }
            });
        }
    </script>
@endpush