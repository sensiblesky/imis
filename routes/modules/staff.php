<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\staff\DashboardController;
use App\Http\Controllers\Modules\staff\ProfileController;
use App\Http\Controllers\Modules\staff\Users\Student\Student;
use App\Http\Controllers\Modules\staff\Notifications\Notifications;
use App\Http\Controllers\Modules\staff\ExamCenters\ExamCenters;

Route::middleware(['auth', 'workspace.access', 'screen.lock'])->prefix('staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('staff.profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('staff.profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('staff.profile.update');
        Route::get('/profile/sessions', [ProfileController::class, 'activeSessions'])->name('staff.profile.sessions');
        Route::delete('/profile/terminate-session', [ProfileController::class, 'terminateSession'])->name('staff.profile.terminateSession');
        Route::get('/session/staff/academic-year', [ProfileController::class, 'UpdateAcademiYear'])->name('session.staff.academic-year.update');






    //students
        Route::get('/users/students', [Student::class, 'StudentIndex'])->name('staff.users.students');
        Route::get('/users/students/view/{uid}', [Student::class, 'StudentView'])->name('staff.users.student.view');
        Route::get('/users/students/edit/{uid}', [Student::class, 'Studentedit'])->name('staff.users.student.edit');
        Route::post('/users/students/update/{uid}', [Student::class, 'StudentUpdate'])->name('staff.users.student.update');
        Route::get('/users/students/create', [Student::class, 'StudentCreate'])->name('staff.users.student.create');
        Route::post('/users/students/store', [Student::class, 'StudentStore'])->name('staff.users.student.store');
        Route::delete('/users/students/delete/{uid}', [Student::class, 'StudentDestroy'])->name('staff.users.student.destroy');
        Route::delete('/users/students/terminate-session/{uid}', [Student::class, 'terminateSession'])->name('staff.users.student.terminateSession');
        Route::post('/users/students/update-identity-print-status/{uid}', [Student::class, 'updateIdPrint'])->name('staff.users.student.printidentity.update');
        Route::post('/users/students/submit-studentid-replacement-form/', [Student::class, 'submitIdReplacementForm'])->name('staff.users.student.identity.renewal');
        Route::post('/users/students/approve-or-reject-renewal/{id}', [Student::class, 'approveOrReject'])->name('staff.users.student.identity.approveOrReject');

    //students




    //route for notifications
        Route::get('/notifications', [Notifications::class, 'getNotifications'])->name('staff.notifications.index');
        Route::get('/notifications/view/{uid}', [Notifications::class, 'viewNotification'])->name('staff.notifications.view');
    //route for notifications



    //ROUTE FOR EXAM CENTERS
        Route::get('/exam-centers/settings', [ExamCenters::class, 'SettingsIndex'])->name('staff.exam.centers.settings');
        Route::get('/exam-centers/settings/view/{uid}', [ExamCenters::class, 'SettingsViewStatistics'])->name('staff.exam-centers.settings.view');
        Route::post('/exam-centers/settings/store', [ExamCenters::class, 'SettingsStore'])->name('staff.exam-centers.settings.store');


        


        Route::get('/exam-centers', [ExamCenters::class, 'index'])->name('staff.exam.centers.index');
        Route::get('/exam-centers/create', [ExamCenters::class, 'create'])->name('staff.exam.centers.create');
        Route::get('/exam-centers/edit/{id}', [ExamCenters::class, 'edit'])->name('staff.exam.centers.edit');
        Route::post('/exam-centers/update/{id}', [ExamCenters::class, 'update'])->name('staff.exam.centers.update');
        Route::delete('/exam-centers/delete/{id}', [ExamCenters::class, 'destroy'])->name('staff.exam.centers.destroy');
    //END HERE


    });
