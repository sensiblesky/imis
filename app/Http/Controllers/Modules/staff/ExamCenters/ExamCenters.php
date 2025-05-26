<?php

namespace App\Http\Controllers\Modules\staff\ExamCenters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExamCenters extends Controller
{

    public function SettingsIndex()
    {
        $userId = auth()->id(); // Get current authenticated user ID

        // Get campuses the user has access to
        $userCampuses = DB::table('user_campuses')
            ->where('user_id', $userId)
            ->pluck('campus_id')
            ->toArray();

        // Fetch settings limited to user's campuses
        $settings = DB::table('staff_exam_centers_settings as s')
            ->leftJoin('base_campuses as c', 's.campus_id', '=', 'c.id')
            ->leftJoin('base_students_semester as sem', 's.semester_id', '=', 'sem.id')
            ->leftJoin('base_students_accademic_years as y', 's.academic_year_id', '=', 'y.id')
            ->leftJoin('staff_exam_centers_enrolled_students as e', 's.id', '=', 'e.staff_exam_centers_settings_id')
            ->whereIn('s.campus_id', $userCampuses)
            ->select(
                's.id',
                's.uid',
                's.campus_id',
                'c.name as campus_name',
                's.semester_id',
                'sem.name as semester_name',
                's.academic_year_id',
                'y.year_range as academic_year_name',
                's.number_of_students as planned_students',
                DB::raw('COUNT(e.id) as enrolled_students'),
                's.status',
                's.created_at',
                's.updated_at'
            )
            ->groupBy(
                's.id',
                's.uid',
                's.campus_id',
                'c.name',
                's.semester_id',
                'sem.name',
                's.academic_year_id',
                'y.year_range',
                's.number_of_students',
                's.status',
                's.created_at',
                's.updated_at'
            )
            ->orderBy('s.created_at', 'desc')
            ->get();

        // Load data for dropdowns
        $campuses = DB::table('base_campuses')
            ->whereIn('id', $userCampuses)
            ->pluck('name', 'id');


        $academicYears = DB::table('base_students_accademic_years')->where('is_current', 1)
            ->pluck('year_range', 'id');

        $semesters = DB::table('base_students_semester')
            ->pluck('name', 'id');

        return view('modules.staff.exam-centers.settings.index', compact(
            'settings',
            'campuses',
            'academicYears',
            'semesters'
        ));
    }





public function SettingsStore(Request $request)
{
    try {
        $entries = $request->input('entries');

        if (!is_array($entries) || count($entries) === 0) {
            return back()->with('error', 'No entries were submitted.');
        }

        $insertData = [];

        foreach ($entries as $index => $entry) {
            // Validate each entry manually using Validator
            $validator = Validator::make($entry, [
                'campus_id' => 'required|integer|exists:base_campuses,id',
                'semester_id' => 'required|integer|exists:base_students_semester,id',
                'academic_year_id' => 'required|integer|exists:base_students_accademic_years,id',
                'number_of_students' => 'required|integer|min:1',
                'status' => 'required|string|in:active,inactive',
                'intake' => 'required|string|in:1,2',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', "Validation failed on entry #" . ($index + 1));


            }

            // Check for existing active setting
            $exists = DB::table('staff_exam_centers_settings')
                ->where('campus_id', $entry['campus_id'])
                ->where('semester_id', $entry['semester_id'])
                ->where('academic_year_id', $entry['academic_year_id'])
                ->where('status', 'active')
                ->where('intake_id', $entry['intake'])
                ->exists();


            if ($exists && $entry['status'] === 'active') {
                return back()
                    ->withInput()
                    ->withErrors(["entry_{$index}" => "Duplicate active setting found for entry #" . ($index + 1) . ". Only one active setting is allowed per combination, othewise set the current one to inactive."]);
            }


            $insertData[] = [
                'campus_id' => $entry['campus_id'],
                'semester_id' => $entry['semester_id'],
                'academic_year_id' => $entry['academic_year_id'],
                'number_of_students' => $entry['number_of_students'],
                'status' => $entry['status'],
                'intake_id' => $entry['intake'],
                'created_by' => auth()->id(), // Assuming you want to set the creator's ID
                'uid' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('staff_exam_centers_settings')->insert($insertData);

        return redirect()->route('staff.exam.centers.settings') ->with('success', 'Settings created successfully.');


    } catch (\Exception $e) {
        Log::error('Error storing exam center settings', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'request' => $request->all(),
        ]);

        return back()->withInput()->with('error', 'An unexpected error occurred. Please try again or contact support.');
    }
}







public function SettingsViewStatistics($uid)
{
    // Step 1: Get the exam center record by UID
    $examCenter = DB::table('staff_exam_centers_settings')
        ->where('uid', $uid)
        ->first();

    if (!$examCenter) {
        return abort(404, 'Exam center not found');
    }

    // Step 2: Get the internal ID of the exam center
    $examCenterId = $examCenter->id;

    //RETURN CENTER NAME BY TAKE CENTER ID AND FETCH IT FROM BASE CAMPUSES
    $examCenterName = DB::table('base_campuses')
        ->where('id', $examCenter->campus_id)
        ->value('name');

    

    // Step 3: Get all students (from users table) enrolled in this exam center
    $students = DB::table('users')
        ->join('users_students', 'users.id', '=', 'users_students.user_id')
        ->join('staff_exam_centers_enrolled_students', 'users.id', '=', 'staff_exam_centers_enrolled_students.user_id')  // Correct join condition
        ->join('base_students_programs', 'users_students.program_id', '=', 'base_students_programs.id')  // Join to get the program code
        ->where('staff_exam_centers_enrolled_students.staff_exam_centers_settings_id', $examCenterId)
        ->where('users.role_id', 2) // Ensure only student users are selected
        ->select('users.*', 'users_students.*', 'base_students_programs.program_code') // Select necessary fields
        ->get();

    return view('modules.staff.exam-centers.settings.statistics', [
        'examCenter' => $examCenter,
        'students' => $students,
        'examCenterName' => $examCenterName,
    ]);
}


}
