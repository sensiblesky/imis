<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>Lock Screen</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="IMIS - Integrated Management Information System" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}"  rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
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
                                            <h5 class="text-primary">Lock screen</h5>
                                            <p>Enter your password to unlock the screen!</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div>
                                    <a href="index.html">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/logo.svg') }}" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                <form method="POST" action="{{ route('auth.screen.unlock') }}">
                                    @csrf

                                    <div class="user-thumb text-center mb-4">
                                        <img src="{{ $authUserPhoto }}" class="rounded-circle img-thumbnail avatar-md" alt="thumbnail">
                                        <h5 class="font-size-15 mt-3">{{ $authUserName }}</h5>
                                    </div>

                                    

                                    <!-- <div class="mb-3">
                                        <label for="userpassword">Password</label>
                                        <input type="password" class="form-control" id="userpassword" name="password" placeholder="Enter password" required>
                                    </div>
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif -->





                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                            id="password" name="password" value="{{ old('password') }}" required autofocus>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    @if(session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif





                                    <div class="text-end">
                                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Unlock</button>
                                    </div>
                                </form>

                                </div>
            
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>Not you ? return <a href="{{ route('logout') }}" class="fw-medium text-primary"> Sign Out </a> </p>
                            <p>© <script>document.write(new Date().getFullYear())</script> {{ $systemSettings->app_name }}. Crafted with <i class="mdi mdi-heart text-danger"></i> by {{ $systemSettings->developer }}</p>
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

    </body>
</html>
