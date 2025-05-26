<?php

namespace App\Http\Controllers\Modules\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Services\SystemMonitorService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        $user = DB::table('users')
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select('users.*', 'base_campuses.name as campus_name')
            ->where('users.id', $authUser->id)
            ->first();

        $totalUsers = DB::table('users')->count();
        $totalStudents = DB::table('users_students')->count();
        $totalStaff = DB::table('users_staff')->count();
        $topCampuses = DB::table('users')
            ->select('base_campuses.name', DB::raw('count(users.id) as total'))
            ->where('users.role_id', '!=', 1)
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->groupBy('users.campus_id', 'base_campuses.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 👇 Get recent auth logs
        $authLogs = DB::table('audit_logs_login_attempts') // or your relevant table
            ->where('user_id', $authUser->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

           // Staff and students grouped by campus
        $staffByCampus = DB::table('users_staff')
            ->join('users', 'users.id', '=', 'users_staff.user_id')
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select('base_campuses.name as campus_name', DB::raw('count(users.id) as total'))
            ->groupBy('base_campuses.name')
            ->pluck('total', 'campus_name');

        $studentsByCampus = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->join('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select('base_campuses.name as campus_name', DB::raw('count(users.id) as total'))
            ->groupBy('base_campuses.name')
            ->pluck('total', 'campus_name');

        // Students grouped by academic year
        $studentsByAcademicYear = DB::table('users_students')
            ->join('base_students_accademic_years', 'users_students.academic_year_id', '=', 'base_students_accademic_years.id')
            ->select('base_students_accademic_years.year_range as year', DB::raw('count(users_students.id) as total'))
            ->groupBy('base_students_accademic_years.year_range')
            ->orderBy('base_students_accademic_years.year_range')
            ->pluck('total', 'year');

        $studentsByProgram = DB::table('users_students')
            ->join('base_students_programs', 'users_students.program_id', '=', 'base_students_programs.id')
            ->select('base_students_programs.program_code', DB::raw('count(users_students.id) as total'))
            ->groupBy('base_students_programs.program_code')
            ->orderBy('base_students_programs.program_code')
            ->pluck('total', 'program_code');



        return view('modules.administrator.dashboard.index', [
            'user' => $user,
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalStaff' => $totalStaff,
            'topCampuses' => $topCampuses,
            'authLogs' => $authLogs,
            'staffByCampus' => $staffByCampus,
            'studentsByCampus' => $studentsByCampus,
            'studentsByAcademicYear' => $studentsByAcademicYear,
            'studentsByProgram' => $studentsByProgram,
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
