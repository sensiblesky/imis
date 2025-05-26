<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\support\DashboardController;
use App\Http\Controllers\Modules\support\ProfileController;
use App\Http\Controllers\Modules\support\Users\Staff\Staff;
use App\Http\Controllers\Modules\support\Users\Student\Student;
use App\Http\Controllers\Modules\support\Notifications\Notifications;

Route::middleware(['auth', 'workspace.access', 'screen.lock'])->prefix('support')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('support.dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('support.profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('support.profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('support.profile.update');
        Route::get('/profile/sessions', [ProfileController::class, 'activeSessions'])->name('support.profile.sessions');
        Route::delete('/profile/terminate-session', [ProfileController::class, 'terminateSession'])->name('support.profile.terminateSession');
        Route::get('/session/academic-year', [ProfileController::class, 'UpdateAcademiYear'])->name('session.academic-year.update');






    //staffs
        Route::get('/users/staffs', [Staff::class, 'StaffIndex'])->name('support.users.staff');
        Route::get('/users/staffs/view/{uid}', [Staff::class, 'StaffView'])->name('support.users.staff.view');
        Route::get('/users/staffs/edit/{uid}', [Staff::class, 'Staffedit'])->name('support.users.staff.edit');
        Route::post('/users/staffs/permissions/{uid}', [Staff::class, 'StaffUpdatePermissions'])->name('support.users.staff.update.permissions');
        Route::get('/users/staffs/permissions/{uid}', [Staff::class, 'StaffPermissions'])->name('support.users.staff.permissions');
        Route::post('/users/staffs/update/{uid}', [Staff::class, 'StaffUpdate'])->name('support.users.staff.update');
        Route::get('/users/staffs/create', [Staff::class, 'StaffCreate'])->name('support.users.staff.create');
        Route::post('/users/staffs/store', [Staff::class, 'StaffStore'])->name('support.users.staff.store');
        Route::delete('/users/staffs/delete/{uid}', [Staff::class, 'StaffDestroy'])->name('support.users.staff.destroy');
        Route::delete('/users/staffs/terminate-session/{uid}', [Staff::class, 'terminateSession'])->name('support.users.staff.terminateSession');
    //staffs


    //students
        Route::get('/users/students', [Student::class, 'StudentIndex'])->name('support.users.students');
        Route::get('/users/students/view/{uid}', [Student::class, 'StudentView'])->name('support.users.student.view');
        Route::get('/users/students/edit/{uid}', [Student::class, 'Studentedit'])->name('support.users.student.edit');
        Route::post('/users/students/update/{uid}', [Student::class, 'StudentUpdate'])->name('support.users.student.update');
        Route::get('/users/students/create', [Student::class, 'StudentCreate'])->name('support.users.student.create');
        Route::post('/users/students/store', [Student::class, 'StudentStore'])->name('support.users.student.store');
        Route::delete('/users/students/delete/{uid}', [Student::class, 'StudentDestroy'])->name('support.users.student.destroy');
        Route::delete('/users/students/terminate-session/{uid}', [Student::class, 'terminateSession'])->name('support.users.student.terminateSession');
        Route::post('/users/students/update-identity-print-status/{uid}', [Student::class, 'updateIdPrint'])->name('support.users.student.printidentity.update');
        Route::post('/users/students/submit-studentid-replacement-form/', [Student::class, 'submitIdReplacementForm'])->name('support.users.student.identity.renewal');
        Route::post('/users/students/approve-or-reject-renewal/{id}', [Student::class, 'approveOrReject'])->name('support.users.student.identity.approveOrReject');

    //students




    //route for notifications
        Route::get('/notifications', [Notifications::class, 'getNotifications'])->name('support.notifications.index');
        Route::get('/notifications/view/{uid}', [Notifications::class, 'viewNotification'])->name('support.notifications.view');
    //route for notifications




    });
