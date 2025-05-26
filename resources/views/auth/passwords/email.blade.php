@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card overflow-hidden">
            <div class="bg-primary-subtle">
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-4">
                            <h5 class="text-primary">Reset Password</h5>
                            <p>Reset your {{ $systemSettings->app_name }} Password</p>
                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0"> 
                <div>
                    <a href="{{ url('/') }}">
                        <div class="avatar-md profile-user-wid mb-4">
                            <span class="avatar-title rounded-circle bg-light">
                                <img src="{{ asset('assets/images/logo.svg') }}" alt="" class="rounded-circle" height="34">
                            </span>
                        </div>
                    </a>
                </div>
                
                <div class="p-2">
                    @if (session('status'))
                        <div class="alert alert-success text-center mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @else
                        <div class="alert alert-success text-center mb-4" role="alert">
                            Enter your Email or phone number and instructions will be sent to you!
                        </div>
                    @endif

                    

                    <form class="form-horizontal" action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="useremail" class="form-label">Email or Phone</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" 
                                   id="useremail" name="identifier" placeholder="Enter email or phone number" 
                                   value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <br>
                            @if ($errors->any())
                                <div class="alert alert-danger text-center mb-4" role="alert">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-5 text-center">
            <p>Remember It ? <a href="{{ route('login') }}" class="fw-medium text-primary">Sign In here</a></p>
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