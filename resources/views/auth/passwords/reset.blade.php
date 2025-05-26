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
                            <p>Set your new password.</p>
                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="p-2">
                    <form class="form-horizontal" method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group auth-pass-inputgroup">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                        id="password" name="password" required>
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

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Confirm Password</label>
                            <div class="input-group auth-pass-inputgroup">
                                <input type="password" class="form-control" 
                                        id="password-confirm" name="password_confirmation" required>
                                <button class="btn btn-light" type="button" id="confirm-password-addon">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-5 text-center">
            <p>Remember your password? <a href="{{ route('login') }}" class="fw-medium text-primary">Login</a></p>
        </div>
    </div>
@endsection

@section('scripts')

@endsection 