<?php

namespace App\Http\Controllers\Modules\Admin\Users\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploadService;
use Jenssegers\Agent\Agent;
use App\Jobs\FetchIpIntelligenceJob;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Student extends Controller
{
    
    public function StudentIndex(Request $request)
{
    $user = auth()->user();

    // Get all campus IDs assigned to the user: from users.campus_id and user_campuses table
    $directCampusId = $user->campus_id;
    $extraCampusIds = DB::table('user_campuses')
        ->where('user_id', $user->id)
        ->pluck('campus_id')
        ->toArray();

    $userCampusIds = array_unique(array_merge([$directCampusId], $extraCampusIds));

    // Lookup data restricted to user's campuses
    $campuses = DB::table('base_campuses')
        ->whereIn('id', $userCampusIds)
        ->pluck('name', 'id');

    $levels = DB::table('base_students_level')
        ->whereIn('campus_id', $userCampusIds)
        ->pluck('level_name', 'id');

    $levelIds = DB::table('base_students_level')
        ->whereIn('campus_id', $userCampusIds)
        ->pluck('id');

    $programs = DB::table('base_students_programs')
        ->whereIn('level_id', $levelIds)
        ->pluck('program_name', 'id');

    $intakes = DB::table('base_students_intakes')->pluck('intake_name', 'id');
    $academicYears = DB::table('base_students_accademic_years')->pluck('year_range', 'id');
    $departments = DB::table('base_departments')->pluck('name', 'id');
    $titles = DB::table('base_titles')->pluck('name', 'id');
    $programsFull = DB::table('base_students_programs')
        ->whereIn('level_id', $levelIds)
        ->select('id', 'program_name', 'level_id')
        ->get();
    


    // Main student query
    $query = DB::table('users')
        ->join('users_students', 'users.id', '=', 'users_students.user_id')
        ->leftJoin('base_students_level', 'users_students.level_id', '=', 'base_students_level.id')
        ->leftJoin('base_students_accademic_years', 'users_students.academic_year_id', '=', 'base_students_accademic_years.id')
        ->leftJoin('base_students_intakes', 'users_students.intake_id', '=', 'base_students_intakes.id')
        ->leftJoin('base_students_programs', 'users_students.program_id', '=', 'base_students_programs.id')
        ->leftJoin('base_students_valid_until', 'users_students.valid_until_id', '=', 'base_students_valid_until.id')
        ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
        ->select(
            'users.id as user_id',
            'users.uid',
            'users.firstname',
            'users.lastname',
            'users.username',
            'users.phone',
            'users.gender',
            'users.email',
            'users.photo',
            'users.status',
            'base_campuses.name as campus_name',
            'base_students_level.level_name',
            'base_students_intakes.intake_name',
            'base_students_programs.program_name',
            'base_students_valid_until.valid_until_date'
        )
        ->where('users.is_deleted', 0);

    // Filtering
    if ($request->filled('firstname')) {
        $query->where('users.firstname', 'like', '%' . $request->firstname . '%');
    }

    if ($request->filled('lastname')) {
        $query->where('users.lastname', 'like', '%' . $request->lastname . '%');
    }

    if ($request->filled('username')) {
        $query->where('users.username', 'like', '%' . $request->username . '%');
    }

    if ($request->filled('email')) {
        $query->where('users.email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('status')) {
        $query->where('users.status', $request->status);
    }
    if ($request->filled('gender')) {
        $query->where('users.gender', $request->gender);
    }

    if ($request->filled('level_id')) {
        $query->where('users_students.level_id', 'like', '%' . $request->level_id . '%');
    }


    if ($request->filled('intake_id')) {
        $query->where('users_students.intake_id', 'like', '%' . $request->intake_id . '%');
    }

    if ($request->filled('program_id')) {
        $query->where('users_students.program_id', 'like', '%' . $request->program_id . '%');
    }

    if ($request->filled('academic_year_id')) {
        $query->where('users_students.academic_year_id', 'like', '%' . $request->academic_year_id . '%');
    }

    

    $studentUsers = $query->get();

    $filteredTotal = (clone $query)->count();

    $filteredMale = (clone $query)->where('users.gender', 'M')->count();
    $filteredFemale = (clone $query)->where('users.gender', 'F')->count();


    return view('modules.administrator.users.student.index', compact(
        'studentUsers', 'campuses', 'programs', 'levels', 'intakes', 'departments', 'titles', 'academicYears', 'programsFull', 'filteredTotal', 'filteredMale', 'filteredFemale'
    ));
}




    public function StudentView($uid)
    {
        // Convert UID to normal user ID
        $userId = DB::table('users')->where('uid', $uid)->pluck('id')->first();

        $student = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->leftJoin('base_students_level', 'users_students.level_id', '=', 'base_students_level.id')
            ->leftJoin('base_students_programs', 'users_students.program_id', '=', 'base_students_programs.id')
            ->leftJoin('base_students_intakes', 'users_students.intake_id', '=', 'base_students_intakes.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->where('users.id', $userId)
            ->select(
                'users.*',
                'base_students_level.level_name as level_name',
                'base_students_programs.program_name as program_name',
                'base_students_intakes.intake_name as intake_name',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'users_students.nhif_id_number',
                'users_students.nida_id_number',
                'users_students.parent_fullname',
                'users_students.relationship_type',
                'users_students.parent_phone',
                'users_students.is_sponsored',
                'users_students.sponsor_name'
            )
            ->first();


        if (!$student) {
            abort(404, 'Student user not found.');
        }


        // Load campuses
        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', $userId)
            ->pluck('base_campuses.name');

        // Load login logs
        $loginLogs = DB::table('audit_logs_login_attempts')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Load general audit logs
        $GeneralLogs = DB::table('audit_logs_general')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Default fallback image path
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        // Try to load and encode student's profile photo
        if ($student && $student->photo) {
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

        // Load session activity
        $sessions = DB::table('sessions')
            ->where('user_id', $userId)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session, $index) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'most_recent' => $index === 0,
                ];
            });


        return view('modules.administrator.users.student.view', compact('student', 'campuses', 'loginLogs', 'sessions', 'GeneralLogs'));
    }

    public function StudentCreate()
    {
        $titles = DB::table('base_titles')->pluck('name', 'id');
        $level = DB::table('base_students_level')->pluck('level_name', 'id');
        // $programs = DB::table('base_students_programs')->pluck('program_name', 'id');
        $programs = DB::table('base_students_programs')->select('program_name', 'id', 'level_id')->get();
        $academicYears = DB::table('base_students_accademic_years')->pluck('year_range', 'id');
        $intakes = DB::table('base_students_intakes')->pluck('intake_name', 'id');
        $validUntil = DB::table('base_students_valid_until')->pluck('valid_until_date', 'id');
        $issponsored = DB::table('base_students_sponsors')->pluck('sponsor_name', 'id');

        $disabilities = DB::table('base_disabilities')->where('status', 'active')->pluck('name', 'id');
        $allCampuses = DB::table('base_campuses')->pluck('name', 'id');
        $relationships = DB::table('base_relationships')->pluck('relationship_name', 'id');
        $insurances = DB::table('base_insurance_companies')->where('status', 'active')->pluck('name', 'id');



        return view('modules.administrator.users.student.create', compact(
            'titles',
            'level',
            'programs',
            'academicYears',
            'intakes',
            'validUntil',
            'issponsored',
            'disabilities',
            'allCampuses',
            'relationships',
            'insurances'
        ));
    }

    public function StudentStore(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string',
            'firstname'       => 'required|string|max:30',
            'middlename'      => 'nullable|string|max:30',
            'lastname'        => 'required|string|max:30',
            'username'        => 'required|string|max:30|unique:users,username',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'nullable|digits:10',
            'gender'          => 'required|in:M,F',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        
            'level_id'        => 'required|exists:base_positions,id',
            'program_id'      => 'required|exists:base_students_programs,id',
            'academic_year'   => 'required|exists:base_students_accademic_years,id',
            'intake_id'       => 'required|exists:base_students_intakes,id',
            'valid_until_id'  => 'required|exists:base_students_valid_until,id',
        
            'nhif_id_number'  => 'nullable|string|max:50',
        
            'parent_fullname'   => 'nullable|string|max:100',
            'relationship_type' => 'nullable|exists:base_relationships,id',
            'parent_phone'      => 'nullable|string|max:20',
            'parent_email'      => 'nullable|email|max:100',
        
            'disability_ids'    => 'nullable|array',
            'disability_ids.*'  => 'exists:base_disabilities,id',
        
            'root_campus'       => 'required|exists:base_campuses,id',
        
            'status'            => 'required|in:active,inactive,suspended',
            'password'          => 'required|string|min:8',
        ]);
        

        DB::beginTransaction();

        try {
            // Insert into users table
            $userId = DB::table('users')->insertGetId([
                'uid' => (string) Str::uuid(),
                'role_id' => 2,
                'firstname' => $validated['firstname'],
                'middlename' => $validated['middlename'],
                'lastname' => $validated['lastname'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'status' => $validated['status'],
                'default_workspace' => "2",
                'campus_id' => $validated['root_campus'],
                'password' => bcrypt($request->input('password')),
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert into users_students table
            DB::table('users_students')->insert([
                'uid' => (string) Str::uuid(),
                'user_id' => $userId,
                'title' => $validated['title'],
                'level_id' => $validated['level_id'],
                'program_id' => $validated['program_id'],
                'academic_year_id' => $validated['academic_year'],
                'intake_id' => $validated['intake_id'],
                'valid_until_id' => $validated['valid_until_id'],
                'nhif_id_number' => data_get($validated, 'nhif_id_number'),
                'parent_fullname' => $validated['parent_fullname'],
                'relationship_type' => $validated['relationship_type'],
                'parent_phone' => $validated['parent_phone'],
                'created_by' => auth()->id(),
            ]);

            // Handle photo upload
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = FileUploadService::upload(
                    $request->file('photo'),
                    'profile_photo',
                    null // No old file on creation
                );

                DB::table('users')->where('id', $userId)->update([
                    'photo' => $photoPath
                ]);
            }

            // Insert into user_disabilities pivot
            if (!empty($validated['disability_ids']) && is_array($validated['disability_ids'])) {
                $disabilityData = array_map(fn($disabilityId) => [
                    'user_id' => $userId,
                    'disability_id' => $disabilityId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['disability_ids']);
                DB::table('users_disabilities')->insert($disabilityData);
            }
            
            

            DB::commit();

            $newData = [
                'user' => DB::table('users')->find($userId),
                'student' => DB::table('users_students')->where('user_id', $userId)->first(),
                'disabilities' => DB::table('users_disabilities')->where('user_id', $userId)->get(),
            ];

            $this->logAction($request, 'Student created', null, $newData, $userId);

            return redirect()->route('admin.users.students')->with('success', 'student account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while saving student.'])->withInput();
        }
    }

    public function Studentedit($uid)
    {
        $userId = DB::table('users')->where('uid', $uid)->value('id');

        if (!$userId) {
            abort(404, 'Student not found.');
        }

        $student = DB::table('users_students')
            ->join('users', 'users.id', '=', 'users_students.user_id')
            ->leftJoin('base_students_level', 'users_students.level_id', '=', 'base_students_level.id')
            ->leftJoin('base_students_programs', 'users_students.program_id', '=', 'base_students_programs.id')
            ->leftJoin('base_students_accademic_years', 'users_students.academic_year_id', '=', 'base_students_accademic_years.id')
            ->leftJoin('base_students_intakes', 'users_students.intake_id', '=', 'base_students_intakes.id')
            ->leftJoin('base_students_valid_until', 'users_students.valid_until_id', '=', 'base_students_valid_until.id')
            ->leftJoin('base_relationships', 'users_students.relationship_type', '=', 'base_relationships.id')  
            ->leftJoin('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->leftJoin('base_titles', 'users_students.title', '=', 'base_titles.id')
            ->where('users.id', $userId)
            ->select(
                'users.*',
                'users_students.title',
                'users_students.level_id',
                'users_students.program_id',
                'users_students.academic_year_id',
                'users_students.intake_id',
                'users_students.valid_until_id',
                'users_students.nhif_id_number',
                'users_students.nida_id_number',
                'users_students.parent_fullname',
                'users_students.relationship_type',
                'users_students.parent_phone',
                'users_students.is_sponsored',
                'users_students.sponsor_name',
                'base_titles.name as title_name',
                'base_students_level.level_name as level_name',
                'base_students_programs.program_name as program_name',
                'base_students_programs.department_id as program_department_id',
                'base_campuses.name as campus_name',
                'base_relationships.relationship_name as relationship_name',
                'user_roles.name as role_name',
                'workspaces.display_name as workspace_name'
            )
            ->first();

        if (!$student) {
            abort(404, 'Student user not found.');
        }


        // Load and encode photo
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';
        if ($student->photo && Storage::disk('public')->exists(ltrim($student->photo, '/'))) {
            $fileContents = Storage::disk('public')->get(ltrim($student->photo, '/'));
            $mimeType = Storage::disk('public')->mimeType(ltrim($student->photo, '/'));
            $student->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
        } else {
            $student->photo_base64 = asset($defaultImagePath);
        }

        // Disabilities



        $disabilities = DB::table('base_disabilities')
            ->where('status', 'active')
            ->pluck('name', 'id'); // This returns ['id' => 'name'] 




        $studentDisabilities = DB::table('users_disabilities')
            ->where('user_id', $userId)
            ->pluck('disability_id')
            ->toArray();

        // Campuses
        $allCampuses = DB::table('base_campuses')->pluck('name', 'id');

        $userCampuses = DB::table('user_campuses')
            ->where('user_id', $userId)
            ->pluck('campus_id')
            ->toArray();

        // Workspaces
        $allWorkspaces = DB::table('workspaces')->pluck('display_name', 'id');

        $userWorkspaces = DB::table('user_workspaces')
            ->where('user_id', $userId)
            ->pluck('workspace_id')
            ->toArray();

        $assignedWorkspaces = DB::table('workspaces')
            ->whereIn('id', $userWorkspaces)
            ->pluck('display_name', 'id');

        // Dropdowns
        $titles = DB::table('base_titles')->pluck('name', 'id');
        $departments = DB::table('base_departments')->pluck('name', 'id');
        $level = DB::table('base_students_level')->pluck('level_name', 'id');
        $programs = DB::table('base_students_programs')->select('program_name', 'id', 'level_id')->get();
        $academicYears = DB::table('base_students_accademic_years')->pluck('year_range', 'id');
        $intakes = DB::table('base_students_intakes')->pluck('intake_name', 'id');
        $validUntil = DB::table('base_students_valid_until')->pluck('valid_until_date', 'id');
        $relationship = DB::table('base_relationships')->pluck('relationship_name', 'id');

        return view('modules.administrator.users.student.edit', compact(
            'student',
            'disabilities',
            'studentDisabilities',
            'allCampuses',
            'userCampuses',
            'allWorkspaces',
            'userWorkspaces',
            'assignedWorkspaces',
            'titles',
            'programs',
            'level',
            'academicYears',
            'intakes',
            'validUntil',
            'relationship',	
        ));
    }

    public function StudentUpdate(Request $request, $uid)
    {
        // 1. Validate all inputs
        $validated = $request->validate([
            'title' => 'required|string',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $uid . ',uid',
            'gender' => 'nullable|string',
            'email' => 'required|string|email|max:255|unique:users,email,' . $uid . ',uid',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|string',
            'program_id' => 'nullable|integer|exists:base_students_programs,id',  // Single program_id validation
            'level_id' => 'required|integer|exists:base_students_level,id',
            'intake_id' => 'required|integer|exists:base_students_intakes,id',
            'academic_year_id' => 'required|integer|exists:base_students_accademic_years,id',
            'parent_fullname' => 'nullable|string|max:255',
            'relationship_type' => 'nullable|integer|exists:base_relationships,id',
            'parent_phone' => 'nullable|string|max:20',
            'valid_until_id' => 'required|integer|exists:base_students_valid_until,id',
            'root_campus' => 'required|integer|exists:base_campuses,id',
            'disability_ids' => 'nullable|array',
            'disability_ids.*' => 'integer|exists:base_disabilities,id',
            'password' => 'nullable|string|min:8|confirmed', // Ensure password confirmation
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
            'default_workspace' => 'nullable|integer|exists:user_workspaces,id',
        ]);

        // 2. Find the user
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        // 3. Check if the selected program matches the student's level
        if (isset($validated['program_id']) && $validated['program_id']) { // Ensure program_id is set
            $program = DB::table('base_students_programs')->where('id', $validated['program_id'])->first();
            if (!$program) {
                return redirect()->back()->with('error', 'Program not found.');
            }

            // Verify if the level_id of the selected program matches the student's level
            if ($program->level_id != $validated['level_id']) {
                return redirect()->back()->with('error', 'The selected program does not match the student\'s level.');
            }
        }

        DB::beginTransaction(); // Always better to use transactions for multiple operations

        try {
            // 4. Update users table
            $userUpdateData = Arr::only($validated, [
                'firstname', 'middlename', 'lastname', 'username', 'gender',
                'email', 'phone', 'status'
            ]);

            $userUpdateData['campus_id'] = $validated['root_campus'];
            $userUpdateData['default_workspace'] = $validated['default_workspace'] ?? null;
            $userUpdateData['password'] = $request->filled('password') ? Hash::make($validated['password']) : $user->password;
            $userUpdateData['updated_by'] = auth()->id();
            $userUpdateData['updated_at'] = now();
            $userUpdateData['role_id'] = 2; // Assuming role_id = 2 is for students

            // 5. Handle profile photo if uploaded
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = FileUploadService::upload(
                    $request->file('photo'),
                    'profile_photo',
                    null // You can add a user subfolder if you want
                );
                $userUpdateData['photo'] = $photoPath;
            }

            // Update users table
            DB::table('users')->where('id', $user->id)->update($userUpdateData);

            // 6. Update users_students table
            $studentUpdateData = Arr::only($validated, [
                'title', 'program_id', 'academic_year_id',
                'valid_until_id', 'is_sponsored', 'sponsor_name',
                'parent_fullname', 'relationship_type', 'parent_phone', 
                'nhif_id_number', 'nida_id_number', 
                'level_id', 'academic_year', 
                'intake_id'
            ]);

            $studentUpdateData['modified_by'] = auth()->id();
            $studentUpdateData['updated_at'] = now();

            // Update users_students table
            DB::table('users_students')->where('user_id', $user->id)->update($studentUpdateData);

            // 7. Update disabilities
            $this->syncUserDisabilities($user->id, $validated['disability_ids'] ?? []);

            // 8. Commit the transaction
            DB::commit();

            // 9. Call audit logger
            $oldData = [
                'user' => (array) $user,
                'student' => (array) DB::table('users_students')->where('user_id', $user->id)->first()
            ];
            
            $newData = [
                'user' => $userUpdateData,
                'student' => $studentUpdateData
            ];
            
            $this->logAction($request, 'student_update', $oldData, $newData, $user->id);
            
            

            // Return success message using session flash
            return redirect()->back()->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error details for debugging
            Log::error('Student update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            // Return error message with session flash
            return redirect()->back()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    protected function syncUserDisabilities($userId, array $disabilityIds)
    {
        DB::table('users_disabilities')->where('user_id', $userId)->delete();

        if (!empty($disabilityIds)) {
            $insertData = array_map(fn($id) => ['user_id' => $userId, 'disability_id' => $id], $disabilityIds);
            DB::table('users_disabilities')->insert($insertData);
        }
    }

    public function terminateSession(Request $request, $uid)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        // Get user ID from UID
        $userId = DB::table('users')->where('uid', $uid)->value('id');

        if (!$userId) {
            return back()->with('error', 'User not found.');
        }

        $sessionId = $request->input('session_id');

        // Ensure the session belongs to the requested user (not the currently authenticated user)
        $session = DB::table('sessions')->where('id', $sessionId)->where('user_id', $userId)->first();

        if (!$session) {
            return back()->with('error', 'Invalid session or unauthorized.');
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Session terminated successfully.');
    }

    public function StudentDestroy($uid)
    {
        // Step 1: Retrieve the student_deletion system setting
        $studentDeletionSetting = DB::table('base_settings_variables')
            ->where('id', '2')
            ->value('value'); 

        $user = DB::table('users')->where('uid', $uid)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['Student not found']);
        }

        // Start transaction to ensure both delete and log are atomic
        DB::beginTransaction();

        try {
            if ($studentDeletionSetting == 0) {
                DB::table('users')
                    ->where('id', $user->id)  // Using ID instead of UID
                    ->update([
                        'is_deleted' => 1,
                        'is_deleted_at' => Carbon::now(),
                        'is_deleted_by' => auth()->id(), // The ID of the logged-in user performing the delete
                    ]);
            } else {
                if ($user->photo && Storage::exists($user->photo)) {
                    Storage::delete($user->photo);
                }
                DB::table('users')->where('id', $user->id)->delete();
            }

            // Manually serialize old data as an array
            $oldData = (array) $user;  // Convert stdClass object to an array

            DB::table('audit_logs_general')->insert([
                'user_id' => auth()->id(),
                'victim_user_id' => $user->id,
                'action' => $studentDeletionSetting == 0 ? 'soft delete' : 'hard delete',
                'model' => 'users',
                'old_data' => json_encode($oldData),  // Convert the old data array to JSON
                'new_data' => $studentDeletionSetting == 1 
                    ? json_encode(['is_deleted' => 1, 'is_deleted_at' => Carbon::now(), 'is_deleted_by' => auth()->id()])
                    : null, // New data after delete (for soft delete)
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.users.students')->with('success', 'Student deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['Failed to delete student: ' . $e->getMessage()]);

        }
    }

    protected function logAction($request, $action, $oldData = null, $newData = null, $id = null)
    {

        
        try {
            $uid = Str::random(32);
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());

            $browser        = $agent->browser();
            $platform       = $agent->platform();
            $deviceType     = $agent->isMobile() ? 'mobile' : 'desktop';
            $requestHeaders = json_encode($request->headers->all());

            $requestIp = $request->header('X-Forwarded-For') ?: $request->ip();

            $logId = DB::table('audit_logs_general')->insertGetId([
                'uid'            => $uid,
                'user_id'        => auth()->id(),
                'victim_user_id' => $id,
                'action'         => $action,
                'model'          => 'UserProfile',
                'old_data'       => $oldData ? json_encode($oldData) : null,
                'new_data'       => $newData ? json_encode($newData) : null,
                'ip_address'     => $requestIp,
                'user_agent'     => $request->userAgent(),
                'created_at'     => now(),
                'browser'        => $browser,
                'platform'       => $platform,
                'device_type'    => $deviceType,
                'request_headers'=> $requestHeaders
            ]);

            $log = DB::table('audit_logs_general')->where('id', $logId)->first();

            dispatch(new \App\Jobs\FetchIpIntelligenceJob($log));
        } catch (\Throwable $e) {
            Log::error('Audit log failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
        }
    }
}
