<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Admin\DashboardController;
use App\Http\Controllers\Modules\Admin\ProfileController;
use App\Http\Controllers\Modules\Admin\System\System;
use App\Http\Controllers\Modules\Admin\Users\Staff\Staff;
use App\Http\Controllers\Modules\Admin\Users\Student\Student;
use App\Http\Controllers\Modules\Admin\Permissions\Permissions;
use App\Http\Controllers\Modules\Admin\Audi\Audi;
use App\Http\Controllers\Modules\Admin\Notifications\Notifications;
use App\Http\Controllers\Modules\Admin\Chats\Chats;
use App\Http\Controllers\Modules\Admin\Backup\BackupController;

Route::middleware(['auth', 'workspace.access', 'screen.lock'])->prefix('admin')->group(function () {
    //profile & dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/profile/sessions', [ProfileController::class, 'activeSessions'])->name('admin.profile.sessions');
        Route::delete('/profile/terminate-session', [ProfileController::class, 'terminateSession'])->name('admin.profile.terminateSession');
        Route::get('/system/usage', [DashboardController::class, 'showSystemUsage']);
        //profile & dashboard

    //system settings
        Route::get('/system', [System::class, 'index'])->name('admin.system');
        Route::post('/system/basic/update', [System::class, 'basic_update'])->name('admin.settings.basic.update');

        Route::get('/system/smtp', [System::class, 'smtp'])->name('admin.system.smtp');
        Route::post('/system/smtp/update', [System::class, 'smtp_update'])->name('admin.system.smtp.update');

        Route::get('/system/sms', [System::class, 'sms'])->name('admin.system.sms');
        Route::post('/system/sms/update', [System::class, 'sms_update'])->name('admin.system.sms.update');


        Route::get('/system/campus', [System::class, 'CampusIndex'])->name('admin.system.campus'); // List campuses
        Route::post('/system/campus/store', [System::class, 'CampusStore'])->name('admin.campus.store'); // Save new campus
        Route::post('/system/campus/update/{uid}', [System::class, 'CampusUpdate'])->name('admin.campus.update'); // Update campus
        Route::delete('/system/campus/delete/{uid}', [System::class, 'CampusDestroy'])->name('admin.campus.destroy'); // Delete campus

        Route::get('/system/jobs', [System::class, 'jobCenter'])->name('admin.system.jobs');
        Route::post('/system/jobs/run', [System::class, 'runJob'])->name('admin.system.jobs.run');

        Route::get('/system/optimize', [System::class, 'Optmization'])->name('admin.system.optimize');
        Route::post('/system/optimize/command', [System::class, 'optimizeCommand'])->name('admin.system.optimize.command'); 

        Route::get('/job-status', function () {
            $output = [];
            $status = 1;
        
            exec("ps aux | grep 'artisan queue:work' | grep -v grep", $output, $status);
        
            return response()->json([
                'running' => !empty($output),
                'output' => $output,
            ]);
        });



        Route::get('/system/info', [System::class, 'sysinfo'])->name('admin.system.info');

    //system settings


    //departement
        Route::get('/system/departments', [System::class, 'DepartmentIndex'])->name('admin.system.department'); // List campuses
        Route::post('/system/departments/store', [System::class, 'DepartmentStore'])->name('admin.department.store'); // Save new campus
        Route::post('/system/departments/update/{uid}', [System::class, 'DepartmentUpdate'])->name('admin.department.update'); // Update campus
        Route::delete('/system/departments/delete/{uid}', [System::class, 'DepartmentDestroy'])->name('admin.department.destroy'); // Delete campus
    //departement
    

    //staffs
        Route::get('/users/staffs', [Staff::class, 'StaffIndex'])->name('admin.users.staff');
        Route::get('/users/staffs/view/{uid}', [Staff::class, 'StaffView'])->name('admin.users.staff.view');
        Route::get('/users/staffs/edit/{uid}', [Staff::class, 'Staffedit'])->name('admin.users.staff.edit');
        Route::post('/users/staffs/permissions/{uid}', [Staff::class, 'StaffUpdatePermissions'])->name('admin.users.staff.update.permissions');
        Route::get('/users/staffs/permissions/{uid}', [Staff::class, 'StaffPermissions'])->name('admin.users.staff.permissions');
        Route::post('/users/staffs/update/{uid}', [Staff::class, 'StaffUpdate'])->name('admin.users.staff.update');
        Route::get('/users/staffs/create', [Staff::class, 'StaffCreate'])->name('admin.users.staff.create');
        Route::post('/users/staffs/store', [Staff::class, 'StaffStore'])->name('admin.users.staff.store');
        Route::delete('/users/staffs/delete/{uid}', [Staff::class, 'StaffDestroy'])->name('admin.users.staff.destroy');
        Route::delete('/users/staffs/terminate-session/{uid}', [Staff::class, 'terminateSession'])->name('admin.users.staff.terminateSession');
    //staffs


    //students
        Route::get('/users/students', [Student::class, 'StudentIndex'])->name('admin.users.students');
        Route::get('/users/students/view/{uid}', [Student::class, 'StudentView'])->name('admin.users.student.view');
        Route::get('/users/students/edit/{uid}', [Student::class, 'Studentedit'])->name('admin.users.student.edit');
        Route::post('/users/students/update/{uid}', [Student::class, 'StudentUpdate'])->name('admin.users.student.update');
        Route::get('/users/students/create', [Student::class, 'StudentCreate'])->name('admin.users.student.create');
        Route::post('/users/students/store', [Student::class, 'StudentStore'])->name('admin.users.student.store');
        Route::delete('/users/students/delete/{uid}', [Student::class, 'StudentDestroy'])->name('admin.users.student.destroy');
        Route::delete('/users/students/terminate-session/{uid}', [Student::class, 'terminateSession'])->name('admin.users.student.terminateSession');
    //students

    //permissions
        Route::get('/permissions', [Permissions::class, 'index'])->name('admin.permissions.index');
        Route::get('/permissions/create', [Permissions::class, 'create'])->name('admin.permissions.create');
        Route::get('/permissions/edit/{id}', [Permissions::class, 'edit'])->name('admin.permissions.edit');
        Route::get('/permissions/show/{id}', [Permissions::class, 'show'])->name('admin.permissions.show');
        Route::post('/permissions/store', [Permissions::class, 'store'])->name('admin.permissions.store');
        Route::post('/permissions/update/{id}', [Permissions::class, 'update'])->name('admin.permissions.update');
        Route::post('/permissions/assign', [Permissions::class, 'assign'])->name('admin.permissions.assign');
        Route::post('/permissions/revoke', [Permissions::class, 'revoke'])->name('admin.permissions.revoke');
        Route::post('/permissions/bulk-assign', [Permissions::class, 'bulkAssign'])->name('admin.permissions.bulk.assign');
        Route::post('/permissions/bulk-revoke', [Permissions::class, 'bulkRevoke'])->name('admin.permissions.bulk.revoke');
        Route::get('/permissions/list', [Permissions::class, 'permissionList'])->name('admin.permissions.list');
        Route::delete('/permissions/delete/{id}', [Permissions::class, 'destroy'])->name('admin.permissions.destroy');
    //permissions


    //route for logs
        Route::get('/audit/logs/authentication', [Audi::class, 'AuthenticationLogs'])->name('admin.logs.authentication');
        Route::get('/audit/logs/authentication/view/{uid}', [Audi::class, 'AuthenticationLogsView'])->name('admin.logs.authentication.view');

        Route::get('/audit/logs/gen', [Audi::class, 'GeneralLogs'])->name('admin.logs.gen');
        Route::get('/audit/logs/gen/view/{uid}', [Audi::class, 'GeneralLogsView'])->name('admin.logs.gen.view');

        Route::get('/audit/logs/logfile', [Audi::class, 'logfile'])->name('admin.logs.logfile');
        Route::post('/audit/logs/logfilr/clear', [Audi::class, 'clearLogfile'])->name('admin.logs.logsfile.clear');
    //ROUTE FOR LOGS



    //route for notifications
        Route::get('/notifications', [Notifications::class, 'getNotifications'])->name('admin.notifications.index');
        Route::get('/notifications/view/{uid}', [Notifications::class, 'viewNotification'])->name('admin.notifications.view');
        Route::post('/notifications/delete/{uid}', [Notifications::class, 'deleteNotification'])->name('admin.notifications.delete');

        Route::get('/notifications/{uid}/edit', [Notifications::class, 'deleteAllNotifications'])->name('admin.notifications.edit');
        Route::post('/notifications/update', [Notifications::class, 'updateNotifications'])->name('admin.notifications.update');
        
        Route::post('/notifications/store', [Notifications::class, 'storeNotifications'])->name('admin.notifications.store');
    //route for notifications




    //route for live chat
        Route::get('/chats', [Chats::class, 'index'])->name('admin.chat.index');
        Route::post('/chats/send', [Chats::class, 'send'])->name('admin.chat.send');
    //route for notifications



    //routes for Data Integrity & Backup
        Route::get('/system/backup', [BackupController::class, 'index'])->name('admin.system.backup');
        Route::post('/system/backup/settings', [BackupController::class, 'storeBackupSettings'])->name('admin.backup.settings');
        Route::get('/system/backup/download', [BackupController::class, 'downloadBackupFile'])->name('admin.backup.download');

    //Data Integrity & Backup
});
