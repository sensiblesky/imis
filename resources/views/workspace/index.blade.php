<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>Select your Workspace</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="IMIS - Integrated Management Information System" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('assets/css/app.min.css') }}"  rel="stylesheet" type="text/css" />
        <!-- App js -->
        <script src="{{ asset('assets/js/plugin.js') }}"></script>

    </head>

    <body>
        
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary-subtle">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Select your Workspace</h5>
                                            <p>Please select the workspace you want to use</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div>
                                    <a href="#">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="assets/images/logo.svg" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                

                                @if(count($workspaces) > 0)
                                    <form id="defaultWorkspaceForm" action="{{ route('workspace.set-default') }}" method="POST">
                                        @csrf
                                        <div class="user-thumb text-center mb-4">
                                            <img src="assets/images/users/avatar-1.jpg" class="rounded-circle img-thumbnail avatar-md" alt="thumbnail">
                                            <h5 class="font-size-15 mt-3">{{ $user->firstname }} {{ $user->lastname }}</h5>
                                        </div>
                                            <select name="workspace_id" id="default_workspace" class="form-select @error('workspace_id') is-invalid @enderror" required>
                                                <option value="">Select Default Workspace</option>
                                                @foreach($workspaces as $workspace)
                                                    <option value="{{ $workspace->id }}" {{ $workspace->is_default ? 'selected' : '' }}>
                                                        {{ $workspace->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('workspace_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <br>
                                            @if ($errors->has('error'))
                                                <div class="alert alert-danger">
                                                    <strong>{{ $errors->first('error') }}</strong>
                                                </div>
                                            @endif

                                        <br>
                                        <div class="text-end">
                                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Continue</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-center">
                                        <div class="alert alert-warning" role="alert">
                                            You don't have any workspaces assigned to your account. Please contact your administrator.
                                        </div>
                                        <br>
                                        @if ($errors->has('error'))
                                            <div class="alert alert-danger">
                                                <strong>{{ $errors->first('error') }}</strong>
                                            </div>
                                        @endif
                                        <a href="{{ route('logout') }}" class="btn btn-primary w-md waves-effect waves-light">Sign Out</a>
                                    </div>
                                @endif
                                </div>
            
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>Not you ? return <a href="{{ route('logout') }}" class="fw-medium text-primary"> Sign Out </a> </p>
                            <p>© <script>document.write(new Date().getFullYear())</script> {{ $systemSettings->app_name }}. Crafted with <i class="mdi mdi-heart text-danger"></i> by {{ env('APP_AUTHOR') }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- JAVASCRIPT -->
        <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        
        <!-- App js -->
        <script src="{{ asset('assets/js/app.js') }}"></script>

        <style>
        .workspaces-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .workspace-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .workspace-card:hover {
            transform: translateY(-5px);
        }

        .workspace-card.default {
            border: 2px solid #4CAF50;
        }

        .user-profile {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            background: white;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }

        .profile-photo {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .profile-initial {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #2196F3;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 1rem;
        }

        .workspace-icon {
            font-size: 24px;
            margin-bottom: 1rem;
            color: #2196F3;
        }

        .workspace-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }
        </style>

        @push('scripts')
        <script>
        document.getElementById('defaultWorkspaceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const workspaceId = document.getElementById('default_workspace').value;
            if (!workspaceId) {
                alert('Please select a workspace');
    </body>
</html>
