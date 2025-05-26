<?php

namespace App\Http\Controllers\Modules\support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    




    public function index()
    {
        $authUser = auth()->user();

        // Get all campus IDs the user has access to
        $accessibleCampusIds = DB::table('user_campuses')
            ->where('user_id', $authUser->id)
            ->pluck('campus_id')
            ->toArray();
        $accessibleCampusIds[] = $authUser->campus_id;
        $accessibleCampusIds = array_unique($accessibleCampusIds);

        // Get logged-in user info
        $user = DB::table('users')
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select('users.*', 'base_campuses.name as campus_name')
            ->where('users.id', $authUser->id)
            ->whereIn('users.campus_id', $accessibleCampusIds)
            ->first();

        // Get academic year ID from session (if available)
        $academicYearId = session('academic_year_id');
        if (!$academicYearId) {
            $academicYearId = DB::table('base_students_accademic_years')
                ->where('is_current', 1)
                ->orderByDesc('created_at')
                ->value('id');
            session(['academic_year_id' => $academicYearId]);
        }

        // Pending student ID issuance
        $pendingIssueIDQuery = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->where('users_students.identity_card', 0)
            ->whereIn('users.campus_id', $accessibleCampusIds);

        // Issued student IDs
        $issuedIDQuery = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->where('users_students.identity_card', 1)
            ->whereIn('users.campus_id', $accessibleCampusIds);

        if ($academicYearId) {
            $pendingIssueIDQuery->where('users_students.academic_year_id', $academicYearId);
            $issuedIDQuery->where('users_students.academic_year_id', $academicYearId);
        }

        // Student ID replacements
        $totalStudentIdReplacementsQuery = DB::table('users_identity_replacements')
            ->join('users', 'users.id', '=', 'users_identity_replacements.user_id')
            ->where('type', 'student_id')
            ->where('users_identity_replacements.status', '1')
            ->where('academic_year_id', $academicYearId)
            ->whereIn('users.campus_id', $accessibleCampusIds);

        // Get counts
        $pendingIssueID = $pendingIssueIDQuery->count();
        $issuedID = $issuedIDQuery->count();
        $totalStudentIdReplacements = $totalStudentIdReplacementsQuery->count();

        // Top campuses
        $topCampuses = DB::table('users')
            ->select('base_campuses.name', DB::raw('count(users.id) as total'))
            ->where('users.role_id', '!=', 1)
            ->whereIn('users.campus_id', $accessibleCampusIds)
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->groupBy('users.campus_id', 'base_campuses.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Authentication logs
        $authLogs = DB::table('audit_logs_login_attempts')
            ->where('user_id', $authUser->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Staff count by campus
        $staffAndStudentByCampus = DB::table('users_staff')
            ->join('users', 'users.id', '=', 'users_staff.user_id')
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->whereIn('users.campus_id', $accessibleCampusIds)
            ->select('base_campuses.name as campus_name', DB::raw('count(users.id) as total'))
            ->groupBy('base_campuses.name')
            ->pluck('total', 'campus_name');

        // Students count by campus
        $studentsByCampusQuery = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->where('users_students.academic_year_id', $academicYearId)
            ->whereIn('users.campus_id', $accessibleCampusIds)
            ->select('base_campuses.name as campus_name', DB::raw('count(users.id) as total'))
            ->groupBy('base_campuses.name');
        $studentsByCampus = $studentsByCampusQuery->pluck('total', 'campus_name');

        // Students count by academic year
        $studentsByAcademicYearQuery = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->join('base_students_accademic_years', 'users_students.academic_year_id', '=', 'base_students_accademic_years.id')
            ->where('users_students.academic_year_id', $academicYearId)
            ->whereIn('users.campus_id', $accessibleCampusIds)
            ->select('base_students_accademic_years.year_range as year', DB::raw('count(users_students.id) as total'))
            ->groupBy('base_students_accademic_years.year_range')
            ->orderBy('base_students_accademic_years.year_range');
        $studentsByAcademicYear = $studentsByAcademicYearQuery->pluck('total', 'year');

        // Students count by program
        $studentsByProgramQuery = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->join('base_students_programs', 'users_students.program_id', '=', 'base_students_programs.id')
            ->where('users_students.academic_year_id', $academicYearId)
            ->whereIn('users.campus_id', $accessibleCampusIds)
            ->select('base_students_programs.program_code', DB::raw('count(users_students.id) as total'))
            ->groupBy('base_students_programs.program_code')
            ->orderBy('base_students_programs.program_code');
        $studentsByProgram = $studentsByProgramQuery->pluck('total', 'program_code');

        // Academic years
        $academicYears = DB::table('base_students_accademic_years')
            ->select('uid', 'year_range')
            ->orderByDesc('id')
            ->get();

        // Return view
        return view('modules.support.dashboard.index', [
            'user' => $user,
            'topCampuses' => $topCampuses,
            'authLogs' => $authLogs,
            'staffAndStudentByCampus' => $staffAndStudentByCampus,
            'studentsByCampus' => $studentsByCampus,
            'studentsByAcademicYear' => $studentsByAcademicYear,
            'studentsByProgram' => $studentsByProgram,
            'pendingIssueID' => $pendingIssueID,
            'issuedID' => $issuedID,
            'totalStudentIdReplacements' => $totalStudentIdReplacements,
            'academicYears' => $academicYears,
        ]);
    }





    public function showSystemUsage(SystemMonitorService $monitor)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Check if the user has access to workspace 5
        $hasAccess = DB::table('user_workspaces')
            ->where('user_id', $user->id)
            ->where('workspace_id', 5)
            ->exists();
    
        if (!$hasAccess) {
            return response()->json(['message' => 'Forbidden: No access to this item'], 403);
        }
    
        $system = $monitor->getUsage();
        return response()->json($system);
    }


    


    

}