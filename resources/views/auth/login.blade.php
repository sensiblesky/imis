@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card overflow-hidden">
            <div class="bg-primary-subtle">
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-4">
                            <h5 class="text-primary">Welcome Back !</h5>
                            <p>Sign in to continue to {{ $systemSettings->app_name }}.</p>
                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0"> 
                <div class="auth-logo">
                    <a href="{{ url('/') }}" class="auth-logo-light">
                        <div class="avatar-md profile-user-wid mb-4">
                            <span class="avatar-title rounded-circle bg-light">
                                <img src="{{ asset('assets/images/logo-light.svg') }}" alt="" class="rounded-circle" height="34">
                            </span>
                        </div>
                    </a>

                    <a href="{{ url('/') }}" class="auth-logo-dark">
                        <div class="avatar-md profile-user-wid mb-4">
                            <span class="avatar-title rounded-circle bg-light">
                                <img src="{{ asset('assets/images/logo.svg') }}" alt="" class="rounded-circle" height="34">
                            </span>
                        </div>
                    </a>
                </div>
                <div class="p-2">
                    <form class="form-horizontal" action="{{ route('login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="login" class="form-label">Username or Email</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" required autofocus>
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group auth-pass-inputgroup">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" placeholder="Enter password" 
                                       aria-label="Password" aria-describedby="password-addon" required>
                                <button class="btn btn-light" type="button" id="password-addon">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" 
                                   name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        <br>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        
                        <div class="mt-3 d-grid">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('password-reset') }}" class="text-muted">
                                <i class="mdi mdi-lock me-1"></i> Forgot your password?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-5 text-center">
            <div>
                <p>© <script>document.write(new Date().getFullYear())</script> {{ $systemSettings->app_name }}. 
                   Crafted with <i class="mdi mdi-heart text-danger"></i> by {{ env('CC') }}</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Password visibility toggle
    document.getElementById('password-addon').addEventListener('click', function() {
        var passwordInput = document.querySelector('input[type="password"]');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>
@endsection 