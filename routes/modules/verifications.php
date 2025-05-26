<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Verification\Exams\ExamCenters;

Route::middleware('web')->group(function () {
    Route::get('/verification/exam-centers', [ExamCenters::class, 'showVerifyForm'])->name('verify.exam.exam-centers');
    Route::post('/verification/exam-centers/verify', [ExamCenters::class, 'verifyExamCenter'])->name('verify.exam.exam-centers.verify');
    Route::get('/verification/exam-centers/students/details/{uid}', [ExamCenters::class, 'showStudentDetails'])->name('verify.exam.exam-centers.students.details');
    Route::post('/verification/exam-centers/enroll', [ExamCenters::class, 'enrollExamCenter'])->name('verify.exam.exam-centers.enroll');
});
