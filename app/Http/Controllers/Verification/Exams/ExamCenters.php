<?php

namespace App\Http\Controllers\Verification\Exams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExamCenters extends Controller
{
    public function showVerifyForm()
    {
        return view('modules.verifications.exam.exam-centers.index');
    }




    public function verifyExamCenter(Request $request)
    {
        $username = $request->input('username');

        // Check if user exists
        $user = DB::table('users')->where('username', $username)->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Student not found with this username.');
        }

        if ($user->role_id != 2) {
            return back()->withInput()->with('error', 'Your registration number appears not to belong to a student.');
        }

        // Get student academic data
        $student = DB::table('users_students')->where('user_id', $user->id)->first();

        if (!$student) {
            return back()->withInput()->with('error', 'Student academic details not found.');
        }

        if ($student->level_id != 4) {
            return back()->withInput()->with('error', 'Sorry, you are not a MASTERS student.');
        }

        // Check each component in staff_exam_centers_settings
        $semesterMatch = DB::table('staff_exam_centers_settings')
            ->where('semester_id', $student->semester_id)
            ->where('status', 'active')
            ->exists();

        if (!$semesterMatch) {
            return back()->withInput()->with('error', "There's no exam center set for your current semester.");
        }

        $intakeMatch = DB::table('staff_exam_centers_settings')
            ->where('intake_id', $student->intake_id)
            ->where('status', 'active')
            ->exists();

        if (!$intakeMatch) {
            return back()->withInput()->with('error', "There's no exam center set for your current intake.");
        }

        $academicYearMatch = DB::table('staff_exam_centers_settings')
            ->where('academic_year_id', $student->academic_year_id)
            ->where('status', 'active')
            ->exists();

        if (!$academicYearMatch) {
            return back()->withInput()->with('error', "There's no exam center set for your current academic year.");
        }

        // All conditions passed
        return redirect()->route('verify.exam.exam-centers.students.details', ['uid' => $user->uid]);
    }





    


    public function showStudentDetails($uid)
    {
        // Fetch student details
        $student = DB::table('users')
            ->join('users_students', 'users.id', '=', 'users_students.user_id')
            ->join('base_students_programs', 'users_students.program_id', '=', 'base_students_programs.id')
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->where('users.uid', $uid)
            ->select(
                'users.uid as user_uid',
                'users.id as user_id',
                'users.firstname',
                'users.middlename',
                'users.lastname',
                'users.username',
                'users.photo',
                'base_campuses.name as campus_name',
                'base_students_programs.program_code as program_name',
                'base_campuses.name as campus_name',
                'base_campuses.id as campus_id'
            )
            ->first();

        if (!$student) {
            return back()->withInput()->with('error', 'Student not found with this registration number.');
        }

        // === PHOTO BASE64 ===
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        if ($student->photo) {
            $photoPath = ltrim($student->photo, '/');
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $student->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $student->photo_base64 = asset($defaultImagePath);
            }
        } else {
            $student->photo_base64 = asset($defaultImagePath);
        }

        // === FETCH ENROLLMENT ===
        $enrollment = DB::table('staff_exam_centers_enrolled_students')
            ->join('staff_exam_centers_settings', 'staff_exam_centers_enrolled_students.staff_exam_centers_settings_id', '=', 'staff_exam_centers_settings.id')
            ->join('base_campuses', 'staff_exam_centers_settings.campus_id', '=', 'base_campuses.id') // Now joining on campus_id
            ->where('staff_exam_centers_enrolled_students.user_id', $student->user_id)
            ->select(
                'staff_exam_centers_enrolled_students.id as enrolled_id',
                'base_campuses.name as enrolled_campus'
            )
            ->first();

        // Debugging to check the user's ID


        // === FETCH AVAILABLE CENTERS (fallback) ===
            $availableCenters = null;
            if (!$enrollment) {
                $availableCenters = DB::table('staff_exam_centers_settings as settings')
                    ->join('base_campuses as campuses', 'settings.campus_id', '=', 'campuses.id')
                    ->leftJoin('staff_exam_centers_enrolled_students as enrolled', 'settings.id', '=', 'enrolled.staff_exam_centers_settings_id')
                    ->where('settings.status', 'active')
                    ->select(
                        'settings.id',
                        'campuses.name as campus_name',
                        'settings.number_of_students',
                        DB::raw('COUNT(enrolled.id) as enrolled_count')
                    )
                    ->groupBy('settings.id', 'campuses.name', 'settings.number_of_students')
                    ->get()
                    ->map(function ($center) {
                        $center->remaining_slots = $center->number_of_students - $center->enrolled_count;
                        return $center;
                    });
            }



        return view('modules.verifications.exam.exam-centers.student-details', compact('student', 'enrollment', 'availableCenters'));
    }





    public function enrollExamCenter(Request $request)
{
    $uid = $request->input('user_id');
    $examCenterId = $request->input('exam_center_id');

    // Get user by UID
    $user = DB::table('users')->where('uid', $uid)->select('id', 'uid', 'role_id')->first();
    if (!$user) {
        return back()->withInput()->with('error', 'Invalid student selected.');
    }

    if ($user->role_id != 2) {
        return back()->withInput()->with('error', 'Selected user is not a student.');
    }

    // Get student academic details
    $student = DB::table('users_students')
        ->where('user_id', $user->id)
        ->select('level_id', 'intake_id', 'semester_id', 'academic_year_id')
        ->first();

    if (!$student) {
        return back()->withInput()->with('error', 'Student academic details not found.');
    }

    if ($student->level_id != 4) {
        return back()->withInput()->with('error', 'Only MASTERS students can be enrolled in exam centers.');
    }

    // Check if student already enrolled
    $alreadyEnrolled = DB::table('staff_exam_centers_enrolled_students')
        ->where('user_id', $user->id)
        ->where('status', '0')
        ->exists();

    if ($alreadyEnrolled) {
        return back()->withInput()->with('error', 'Student is already enrolled in an exam center.');
    }

    // Get exam center details
    $setting = DB::table('staff_exam_centers_settings')
        ->where('id', $examCenterId)
        ->where('status', 'active')
        ->first();

    if (!$setting) {
        return back()->withInput()->with('error', 'Selected exam center is invalid.');
    }

    // Validate match for intake, semester, academic year
    if ($setting->semester_id != $student->semester_id) {
        return back()->withInput()->with('error', "This exam center doesn't match the student's semester.");
    }

    if ($setting->intake_id != $student->intake_id) {
        return back()->withInput()->with('error', "This exam center doesn't match the student's intake.");
    }

    if ($setting->academic_year_id != $student->academic_year_id) {
        return back()->withInput()->with('error', "This exam center doesn't match the student's academic year.");
    }

    // Check center capacity
    $enrolledCount = DB::table('staff_exam_centers_enrolled_students')
        ->where('staff_exam_centers_settings_id', $setting->id)
        ->count();

    if ($enrolledCount >= $setting->number_of_students) {
        return back()->withInput()->with('error', 'Selected exam center is already full.');
    }

    // Enroll student
    DB::table('staff_exam_centers_enrolled_students')->insert([
        'user_id' => $user->id,
        'staff_exam_centers_settings_id' => $setting->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('verify.exam.exam-centers.students.details', ['uid' => $user->uid])
        ->with('success', 'Student successfully enrolled in exam center.');
}


}
