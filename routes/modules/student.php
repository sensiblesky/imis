<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Admin\DashboardController;
use App\Http\Controllers\Modules\Admin\ProfileController;
use App\Http\Controllers\Modules\Admin\System\System;
use App\Http\Controllers\Modules\Admin\Users\Staff\Staff;
use App\Http\Controllers\Modules\Admin\Users\Student\Student;


Route::middleware(['auth', 'workspace.access'])->prefix('student')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('students.dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('students.profile');
    

});
