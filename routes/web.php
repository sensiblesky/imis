<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Workspace\Workspace;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ScreenSession;

Route::middleware(['maintenance'])->group(function () {
    Route::middleware('web')->group(function () {
        Route::redirect('/', 'auth/login');
        Route::get('auth/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('auth/login', [LoginController::class, 'login'])->name('login.submit');
        Route::get('auth/password/reset', [PasswordResetController::class, 'passwordResetForm'])->name('password-reset');
        Route::post('auth/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
        Route::get('auth/password/reset/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset.form');
        Route::post('auth/password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
        Route::get('/auth/logout', [LoginController::class, 'logout'])->name('logout');




    });

    Route::middleware('auth')->group(function () {
        Route::get('welcome', [Workspace::class, 'workspaceHome'])->name('welcome');
        Route::post('/workspace/set-default', [Workspace::class, 'setDefault'])->name('workspace.set-default');

        Route::post('/send-otp', [TwoFactorController::class, 'sendOtp'])->name('send.otp');
        Route::post('/verify-otp', [TwoFactorController::class, 'verifyOtp'])->name('verify.otp');



        Route::get('/two-auth/select-method', [TwoFactorController::class, 'showMethodSelection'])->name('twofa.select-method');
        Route::post('/two-auth/select-method', [TwoFactorController::class, 'submitMethodSelection'])->name('twofa.submit-method');

        Route::get('/two-auth/email/verify', [TwoFactorController::class, 'showVerifyOtpForm'])->name('twofa.verify-otp');
        Route::post('/two-auth/email/verify', [TwoFactorController::class, 'verifyEmailOtp'])->name('twofa.verify-email-otp');
        Route::get('/two-auth/success', [TwoFactorController::class, 'showSuccess'])->name('twofa.success');

        Route::post('/two-aut/deactivate', [TwoFactorController::class, 'deactivate'])->name('twofa.deactivate');


        Route::get('/two-auth/google', [TwoFactorController::class, 'showGoogleSetup'])->name('twofa.google');
        Route::post('/two-auth/google/verify', [TwoFactorController::class, 'verifyGoogleOtp'])->name('twofa.google.verify');


        Route::get('/auth/screen/locked', [ScreenSession::class, 'locked'])->name('auth.screen.locked');
        Route::post('/auth/screen/lock', [ScreenSession::class, 'lock'])->name('auth.screen.lock');
        Route::post('/auth/screen/unlock', [ScreenSession::class, 'unlock'])->name('auth.screen.unlock');

    });

    require base_path('routes/modules/admin.php');
    require base_path('routes/modules/support.php');
    require base_path('routes/base/base.php');
    require base_path('routes/modules/student.php');
    require base_path('routes/modules/staff.php');
    require base_path('routes/modules/verifications.php');
});