<?php

namespace App\Http\Controllers\Modules\Admin\Users\Staff;

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
use Illuminate\Support\Facades\Validator;


class Staff extends Controller
{
    public function StaffIndex(Request $request)
    {
        // Validation
        $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:0,1',
            'base_title_id' => 'nullable|integer|exists:base_titles,id',
            'base_department_id' => 'nullable|integer|exists:base_departments,id',
            'base_position_id' => 'nullable|integer|exists:base_positions,id',
            'campus_id' => 'nullable|integer|exists:base_campuses,id',
            'gender' => 'nullable|in:M,F',
        ]);

        // Get logged-in user's allowed campuses
        $user = auth()->user();
        $directCampusId = $user->campus_id;
        $extraCampusIds = DB::table('user_campuses')
            ->where('user_id', $user->id)
            ->pluck('campus_id')
            ->toArray();

        $userCampusIds = array_unique(array_merge([$directCampusId], $extraCampusIds));
        $campuses = DB::table('base_campuses')
            ->whereIn('id', $userCampusIds)
            ->pluck('name', 'id');

        // Main query
        $query = DB::table('users')
            ->join('users_staff', 'users.id', '=', 'users_staff.user_id')
            ->leftJoin('base_titles', 'users_staff.base_title_id', '=', 'base_titles.id')
            ->leftJoin('base_departments', 'users_staff.base_department_id', '=', 'base_departments.id')
            ->leftJoin('base_positions', 'users_staff.base_position_id', '=', 'base_positions.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->where('users.role_id', 1)
            ->where('users.is_deleted', 0)
            ->whereIn('users.campus_id', $userCampusIds)
            ->select(
                'users.id as user_id',
                'users.uid as staff_uid',
                'users.firstname',
                'users.lastname',
                'users.username',
                'users.gender',
                'users.phone',
                'users.email',
                'users.photo',
                'users.status',
                'users_staff.*',
                'base_titles.name as title_name',
                'base_departments.name as department_name',
                'base_positions.name as position_name',
                'base_campuses.name as campus_name'
            );

        // Apply filters
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
        if ($request->filled('phone')) {
            $query->where('users.phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->filled('status')) {
            $query->where('users.status', $request->status);
        }
        if ($request->filled('campus_id') && in_array($request->campus_id, $userCampusIds)) {
            $query->where('users.campus_id', $request->campus_id);
        }
        if ($request->filled('gender')) {
            $query->where('users.gender', $request->gender);
        }
        if ($request->filled('base_title_id')) {
            $query->where('users_staff.base_title_id', $request->base_title_id);
        }
        if ($request->filled('base_department_id')) {
            $query->where('users_staff.base_department_id', $request->base_department_id);
        }
        if ($request->filled('base_position_id')) {
            $query->where('users_staff.base_position_id', $request->base_position_id);
        }

        // Get results
        $staffUsers = $query->get();

        // Filter stats
        $filteredTotal = (clone $query)->count();
        $filteredMale = (clone $query)->where('users.gender', 'M')->count();
        $filteredFemale = (clone $query)->where('users.gender', 'F')->count();

        // Dropdown options
        $titles = DB::table('base_titles')->select('id', 'name')->get();
        $departments = DB::table('base_departments')->select('id', 'name')->get();
        $positions = DB::table('base_positions')->select('id', 'name')->get();

        return view('modules.administrator.users.staff.index', compact(
            'staffUsers',
            'titles',
            'departments',
            'positions',
            'filteredTotal',
            'filteredMale',
            'filteredFemale',
            'campuses',
            'userCampusIds'
        ));
    }









    public function StaffView($uid)
    {
        // Convert UID to internal user ID
        $userId = DB::table('users')->where('uid', $uid)->pluck('id')->first();

        if (!$userId) {
            abort(404, 'Staff user not found.');
        }

        $staffCampusId = DB::table('users')->where('id', $userId)->value('campus_id');

        // Get the current user's allowed campuses
        $authUser = auth()->user();
        $allowedCampusIds = array_unique(array_merge(
            [$authUser->campus_id],
            DB::table('user_campuses')->where('user_id', $authUser->id)->pluck('campus_id')->toArray()
        ));

        // Check if this staff's campus is within allowed list
        if (!in_array($staffCampusId, $allowedCampusIds)) {
            $this->logAction(request(), 'view', null, null, $userId, 403, "Unauthorized to view this staff.");
            abort(403, 'Unauthorized to view this staff. Please ensure you have the right access permissions');
        }

        // Now safe to fetch full staff data
        $staff = DB::table('users_staff')
            ->join('users', 'users.id', '=', 'users_staff.user_id')
            ->leftJoin('base_titles', 'users_staff.base_title_id', '=', 'base_titles.id')
            ->leftJoin('base_departments', 'users_staff.base_department_id', '=', 'base_departments.id')
            ->leftJoin('base_positions', 'users_staff.base_position_id', '=', 'base_positions.id')
            ->leftJoin('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->where('users.id', $userId)
            ->select(
                'users.*',
                'base_titles.name as title',
                'base_departments.name as department',
                'base_positions.name as position',
                'users_staff.identity_print_status',
                'users_staff.date_issued',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'workspaces.display_name as workspace_name'
            )
            ->first();

        if (!$staff) {
            abort(404, 'Staff user not found.');
        }

        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', $userId)
            ->pluck('base_campuses.name');

        $loginLogs = DB::table('audit_logs_login_attempts')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $GeneralLogs = DB::table('audit_logs_general')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Handle photo
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        if ($staff && $staff->photo) {
            $photoPath = ltrim($staff->photo, '/');
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $staff->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $staff->photo_base64 = asset($defaultImagePath);
            }
        } else {
            $staff->photo_base64 = asset($defaultImagePath);
        }

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

        return view('modules.administrator.users.staff.view', compact('staff', 'campuses', 'loginLogs', 'sessions', 'GeneralLogs'));
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



     public function StaffEdit(Request $request, $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();

        if (!$user) {
            abort(404, 'Staff user not found.');
        }

        $userId = $user->id;

        // Collect all campuses of the target user (single + multiple)
        $targetUserCampuses = DB::table('user_campuses')
            ->where('user_id', $userId)
            ->pluck('campus_id')
            ->toArray();

        if ($user->campus_id && !in_array($user->campus_id, $targetUserCampuses)) {
            $targetUserCampuses[] = $user->campus_id;
        }

        // Get campuses of the currently authenticated user
        $currentUser = auth()->user();

        $staffUserCampuses = DB::table('user_campuses')
            ->where('user_id', $currentUser->id)
            ->pluck('campus_id')
            ->toArray();

        if ($currentUser->campus_id && !in_array($currentUser->campus_id, $staffUserCampuses)) {
            $staffUserCampuses[] = $currentUser->campus_id;
        }

        // Only allow access if there's any shared campus
        $commonCampuses = array_intersect($targetUserCampuses, $staffUserCampuses);

        if (empty($commonCampuses)) {
            $this->logAction($request, 'view', null, null, $userId, 403, "Unauthorized to view edit this user.");
            abort(403, 'Access denied. You are not authorized to edit this staff');
        }

        $staff = DB::table('users_staff')
            ->join('users', 'users.id', '=', 'users_staff.user_id')
            ->leftJoin('base_titles', 'users_staff.base_title_id', '=', 'base_titles.id')
            ->leftJoin('base_departments', 'users_staff.base_department_id', '=', 'base_departments.id')
            ->leftJoin('base_positions', 'users_staff.base_position_id', '=', 'base_positions.id')
            ->leftJoin('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->where('users.id', $userId)
            ->select(
                'users.*',
                'base_titles.name as title',
                'base_departments.name as department',
                'base_positions.name as position',
                'users_staff.identity_print_status',
                'users_staff.date_issued',
                'users_staff.base_title_id',
                'users_staff.base_department_id',
                'users_staff.base_position_id',
                'users_staff.uid as staff_uid',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'workspaces.display_name as workspace_name'
            )
            ->first();

        if (!$staff) {
            abort(404, 'Staff user not found.');
        }

        // Load and encode photo
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';
        if ($staff->photo && Storage::disk('public')->exists(ltrim($staff->photo, '/'))) {
            $fileContents = Storage::disk('public')->get(ltrim($staff->photo, '/'));
            $mimeType = Storage::disk('public')->mimeType(ltrim($staff->photo, '/'));
            $staff->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
        } else {
            $staff->photo_base64 = asset($defaultImagePath);
        }

        $disabilities = DB::table('base_disabilities')
            ->where('status', 'active')
            ->pluck('name', 'id');

        // 🔒 Only return campuses assigned to the authenticated staff user
        $allCampuses = DB::table('base_campuses')
            ->whereIn('id', $staffUserCampuses)
            ->pluck('name', 'id');

        $userCampuses = DB::table('user_campuses')
            ->where('user_id', $userId)
            ->pluck('campus_id')
            ->toArray();

        $allWorkspaces = DB::table('workspaces')
            ->join('user_workspaces', 'workspaces.id', '=', 'user_workspaces.workspace_id')
            ->where('user_workspaces.user_id', auth()->id())
            ->pluck('workspaces.display_name', 'workspaces.id');


        $userWorkspaces = DB::table('user_workspaces')
            ->where('user_id', $userId)
            ->pluck('workspace_id')
            ->toArray();

        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', $userId)
            ->pluck('base_campuses.name');

        $titles = DB::table('base_titles')->pluck('name', 'id');
        $departments = DB::table('base_departments')->pluck('name', 'id');
        $positions = DB::table('base_positions')->pluck('name', 'id');

        $assignedWorkspaces = DB::table('workspaces')
            ->whereIn('id', $userWorkspaces)
            ->pluck('display_name', 'id');

        $staffDisabilities = DB::table('users_disabilities')
            ->where('user_id', $staff->id)
            ->pluck('disability_id')
            ->toArray();

        return view('modules.administrator.users.staff.edit', compact(
            'staff',
            'disabilities',
            'allCampuses',
            'userCampuses',
            'allWorkspaces',
            'assignedWorkspaces',
            'userWorkspaces',
            'campuses',
            'titles',
            'departments',
            'positions',
            'staffDisabilities',
        ));
    }


    public function StaffUpdate(Request $request, $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $id = $user->id;
        $authUser = auth()->user();

        // Get all campus IDs of the authenticated user (default + additional)
        $authUserCampusIds = array_unique(array_merge(
            [$authUser->campus_id],
            DB::table('user_campuses')->where('user_id', $authUser->id)->pluck('campus_id')->toArray()
        ));

        // Check if the target user's default campus is in auth user's campus list
        if (!in_array($user->campus_id, $authUserCampusIds)) {
            $this->logAction($request, 'update', null, null, $id, 403, "Unauthorized to edit this user.");
            abort(403, 'Unauthorized to edit this user. Please ensure you have the right access permissions of Campus ID: ' . $user->campus_id);
        }

        $oldData = [
            'user' => $user,
            'staff' => DB::table('users_staff')->where('user_id', $id)->first(),
            'campuses' => DB::table('user_campuses')->where('user_id', $id)->pluck('campus_id')->toArray(),
            'workspaces' => DB::table('user_workspaces')->where('user_id', $id)->pluck('workspace_id')->toArray(),
        ];

        // Validate and collect user data
        $validated = $request->validate([
            'username'    => 'required|string|min:3|max:50|alpha_dash|unique:users,username,' . $id,
            'firstname'   => 'required|string|min:2|max:100',
            'middlename'  => 'nullable|string|min:2|max:100',
            'lastname'    => 'required|string|min:2|max:100',
            'email'       => 'required|email|max:100|unique:users,email,' . $id,
            'phone'       => 'nullable|string|min:7|max:20',
            'gender'      => 'required|in:M,F',
            'status'      => 'required|in:active,inactive,suspended',
            'root_campus' => 'required|exists:base_campuses,id',
            'password'    => [
                'nullable',
                'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols()
            ],
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'username'    => $request->input('username'),
                'firstname'   => $request->input('firstname'),
                'middlename'  => $request->input('middlename'),
                'lastname'    => $request->input('lastname'),
                'email'       => $request->input('email'),
                'phone'       => $request->input('phone'),
                'gender'      => $request->input('gender'),
                'status'      => $request->input('status'),
                'campus_id'   => $request->input('root_campus'),
            ];

            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photo = FileUploadService::upload(
                    $request->file('photo'),
                    'profile_photo',
                    $user->photo
                );
                $userData['photo'] = $photo;
            }

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->input('password'));
            }

            // Default workspace (only set if it exists in user_workspaces)
            $defaultWorkspace = $request->input('default_workspace');
            $workspaceValid = DB::table('user_workspaces')
                ->where('user_id', $id)
                ->where('workspace_id', $defaultWorkspace)
                ->exists();

            $userData['default_workspace'] = $workspaceValid ? $defaultWorkspace : null;

            // Update users table
            DB::table('users')->where('id', $id)->update($userData);

            // Update or insert into users_staff
            $staffData = [
                'base_title_id'      => $request->input('title'),
                'base_department_id' => $request->input('base_department_id'),
                'base_position_id'   => $request->input('base_position_id'),
                'modified_by'        => auth()->id(),
            ];
            DB::table('users_staff')->updateOrInsert(['user_id' => $id], $staffData);

            // Sync campuses
            DB::table('user_campuses')->where('user_id', $id)->delete();
            foreach ($request->input('campuses', []) as $campusId) {
                DB::table('user_campuses')->insert([
                    'user_id' => $id,
                    'campus_id' => $campusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Sync workspaces
            DB::table('user_workspaces')->where('user_id', $id)->delete();
            foreach ($request->input('workspaces', []) as $workspaceId) {
                DB::table('user_workspaces')->insert([
                    'user_id' => $id,
                    'workspace_id' => $workspaceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Sync disabilities
            DB::table('users_disabilities')->where('user_id', $id)->delete();
            foreach ($request->input('disability_ids', []) as $disabilityId) {
                DB::table('users_disabilities')->insert([
                    'user_id' => $id,
                    'disability_id' => $disabilityId,
                    'created_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logAction($request, 'update', $oldData, null, $id, 500, "Update failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update staff. Please try again.');
        }

        $newData = [
            'user' => DB::table('users')->where('id', $id)->first(),
            'staff' => DB::table('users_staff')->where('user_id', $id)->first(),
            'campuses' => DB::table('user_campuses')->where('user_id', $id)->pluck('campus_id')->toArray(),
            'workspaces' => DB::table('user_workspaces')->where('user_id', $id)->pluck('workspace_id')->toArray(),
        ];

        $this->logAction($request, 'update', $oldData, $newData, $id, 200, "Staff updated successfully.");

        return redirect()->back()->with('success', 'Staff updated successfully.');
    }



    public function StaffCreate()
    {
        $titles = DB::table('base_titles')->pluck('name', 'id');
        $departments = DB::table('base_departments')->pluck('name', 'id');
        $positions = DB::table('base_positions')->pluck('name', 'id');
        $disabilities = DB::table('base_disabilities')
            ->where('status', 'active')
            ->pluck('name', 'id');

        $currentUser = auth()->user();

        // Get campuses from user_campuses
        $staffUserCampuses = DB::table('user_campuses')
            ->where('user_id', $currentUser->id)
            ->pluck('campus_id')
            ->toArray();

        // Include users.campus_id if not already in the array
        if ($currentUser->campus_id && !in_array($currentUser->campus_id, $staffUserCampuses)) {
            $staffUserCampuses[] = $currentUser->campus_id;
        }

        // Fetch only those campuses
        $allCampuses = DB::table('base_campuses')
            ->whereIn('id', $staffUserCampuses)
            ->pluck('name', 'id');

        $allWorkspaces = DB::table('workspaces')
            ->join('user_workspaces', 'workspaces.id', '=', 'user_workspaces.workspace_id')
            ->where('user_workspaces.user_id', auth()->id())
            ->pluck('workspaces.display_name', 'workspaces.id');


        return view('modules.administrator.users.staff.create', compact(
            'titles',
            'departments',
            'positions',
            'disabilities',
            'allCampuses',
            'allWorkspaces'
        ));
    }



    public function StaffStore(Request $request)
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

            'position_id'     => 'required|exists:base_positions,id',
            'department_id'   => 'required|exists:base_departments,id',
            'disability_ids'  => 'nullable|array',
            'disability_ids.*'=> 'exists:base_disabilities,id',

            'root_campus'     => 'required|exists:base_campuses,id',
            'campuses'        => 'nullable|array',
            'campuses.*'      => 'exists:base_campuses,id',

            'status'          => 'required|in:active,inactive,suspended',

            'workspaces'      => 'nullable|array',
            'workspaces.*'    => 'exists:workspaces,id',
        ]);

        // ✅ Get allowed campuses for current user
        $currentUser = auth()->user();
        $allowedCampuses = DB::table('user_campuses')
            ->where('user_id', $currentUser->id)
            ->pluck('campus_id')
            ->toArray();

        // Include users.campus_id (if not null and not already included)
        if ($currentUser->campus_id && !in_array($currentUser->campus_id, $allowedCampuses)) {
            $allowedCampuses[] = $currentUser->campus_id;
        }

        // ✅ Check if root_campus is allowed
        if (!in_array($validated['root_campus'], $allowedCampuses)) {
            $this->logAction($request, 'create', null, null, null, 403, "Unauthorized to create user and assign this root campus.");
            return back()->withErrors(['root_campus' => 'You are not allowed to assign user on this campus.'])->withInput();
        }

        // ✅ Check if all additional campuses are allowed
        if (!empty($validated['campuses'])) {
            foreach ($validated['campuses'] as $campusId) {
                if (!in_array($campusId, $allowedCampuses)) {
                    $this->logAction($request, 'create', null, null, null, 403, "Unauthorized to create user and assign one or more selected campuses.");
                    return back()->withErrors(['campuses' => 'You are not allowed to assign one or more selected campuses.'])->withInput();
                }
            }
        }

        DB::beginTransaction();

        try {
            // Insert into users table
            $userId = DB::table('users')->insertGetId([
                'uid' => (string) Str::uuid(),
                'role_id' => 1,
                'firstname' => $validated['firstname'],
                'middlename' => $validated['middlename'],
                'lastname' => $validated['lastname'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'status' => $validated['status'],
                'default_workspace' => $validated['workspaces'][0] ?? null,
                'campus_id' => $validated['root_campus'],
                'password' => bcrypt($request->input('password')),
                'created_by' => $currentUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert into users_staff
            DB::table('users_staff')->insert([
                'user_id' => $userId,
                'base_title_id' => $validated['title'],
                'base_position_id' => $validated['position_id'],
                'base_department_id' => $validated['department_id'],
                'created_by' => $currentUser->id,
            ]);

            // Upload photo if exists
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = FileUploadService::upload(
                    $request->file('photo'),
                    'profile_photo',
                    null
                );

                DB::table('users')->where('id', $userId)->update(['photo' => $photoPath]);
            }

            // Insert into user_campuses
            if (!empty($validated['campuses'])) {
                $campusData = array_map(fn($campusId) => [
                    'user_id' => $userId,
                    'campus_id' => $campusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['campuses']);
                DB::table('user_campuses')->insert($campusData);
            }

            // Insert into users_disabilities
            if (!empty($validated['disability_ids'])) {
                $disabilityData = array_map(fn($id) => [
                    'user_id' => $userId,
                    'disability_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['disability_ids']);
                DB::table('users_disabilities')->insert($disabilityData);
            }

            // Insert into user_workspaces
            if (!empty($validated['workspaces'])) {
                $workspaceData = array_map(fn($id) => [
                    'user_id' => $userId,
                    'workspace_id' => $id,
                    'created_by' => $currentUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['workspaces']);
                DB::table('user_workspaces')->insert($workspaceData);
            }

            DB::commit();

            $newData = [
                'user' => DB::table('users')->find($userId),
                'staff' => DB::table('users_staff')->where('user_id', $userId)->first(),
                'workspaces' => DB::table('user_workspaces')->where('user_id', $userId)->get(),
                'campuses' => DB::table('user_campuses')->where('user_id', $userId)->get(),
                'disabilities' => DB::table('users_disabilities')->where('user_id', $userId)->get(),
            ];

            $this->logAction($request, 'create', null, $newData, $userId, 200, "Staff created successfully.");

            return redirect()->route('admin.users.staff')->with('success', 'Staff account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while saving staff.'])->withInput();
        }
    }




    public function StaffDestroy($uid)
    {
        $staffDeletionSetting = DB::table('base_settings_variables')
            ->where('id', '1')
            ->value('value'); 

        $user = DB::table('users')->where('uid', $uid)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['User not found']);
        }

        // Start transaction to ensure both delete and log are atomic
        DB::beginTransaction();

        try {
            if ($staffDeletionSetting == 0) {
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
                'action' => $staffDeletionSetting == 0 ? 'soft delete' : 'hard delete',
                'model' => 'users',
                'old_data' => json_encode($oldData),  // Convert the old data array to JSON
                'new_data' => $staffDeletionSetting == 1 
                    ? json_encode(['is_deleted' => 1, 'is_deleted_at' => Carbon::now(), 'is_deleted_by' => auth()->id()])
                    : null, // New data after delete (for soft delete)
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.users.staff')->with('success', 'User deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['Failed to delete user: ' . $e->getMessage()]);

        }
    }
















    public function StaffPermissions($uid)
    {
        // Get the staff user
        $staff = DB::table('users')
            ->where('uid', $uid)
            ->first();

        if (!$staff) {
            abort(404, 'Staff user not found.');
        }

        // Get the authenticated user's campus_id
        $userCampusId = auth()->user()->campus_id;

        // Check if the authenticated user's campus_id matches the target staff's campus_id
        if ($userCampusId !== $staff->campus_id) {
            abort(403, 'You do not have permission to access this staff member\'s permissions.');
        }

        // Get all workspaces of the user
        $userWorkspaceIds = DB::table('user_workspaces')
            ->where('user_id', $staff->id)
            ->pluck('workspace_id')
            ->toArray();

        // Get all workspaces with related permissions
        $workspaces = DB::table('workspaces')
            ->whereIn('id', $userWorkspaceIds)
            ->orderBy('order')
            ->get();

        // Get all permissions related to those workspaces
        $permissions = DB::table('base_permissions')
            ->whereIn('workspace_id', $userWorkspaceIds)
            ->orderBy('group_name')
            ->orderBy('name')
            ->get();

        // Group permissions by workspace_id first
        $permissionsByWorkspace = $permissions->groupBy('workspace_id');

        // Get current permissions assigned to the staff
        $userPermissionIds = DB::table('users_permissions')
            ->where('user_id', $staff->id)
            ->pluck('permission_id')
            ->toArray();

        return view('modules.administrator.users.staff.permissions', compact(
            'staff',
            'workspaces',
            'permissionsByWorkspace',
            'userPermissionIds'
        ));
    }













public function StaffUpdatePermissions(Request $request, $uid)
{
    $authUserId = auth()->id();

    // Get the staff user
    $staff = DB::table('users')->where('uid', $uid)->first();
    if (!$staff) {
        return redirect()->back()->with('error', 'Staff user not found.');
    }

    // Get the authenticated user's campus_id
    $userCampusId = auth()->user()->campus_id;

    // Check if the authenticated user's campus_id matches the target staff's campus_id
    if ($userCampusId !== $staff->campus_id) {
        $this->logAction($request, 'update', null, null, $staff->id, 403, "Unauthorized to update this staff member's permissions.");
        return redirect()->back()->with('error', 'You do not have permission to update this staff member\'s permissions due to campus mismatch.');
    }

    // Validate incoming permission IDs
    $request->validate([
        'permissions' => 'nullable|array',
        'permissions.*' => 'integer|exists:base_permissions,id',
    ]);

    // Get shared workspaces between admin and staff
    $adminWorkspaceIds = DB::table('user_workspaces')
        ->where('user_id', $authUserId)
        ->pluck('workspace_id')
        ->toArray();

    $staffWorkspaceIds = DB::table('user_workspaces')
        ->where('user_id', $staff->id)
        ->pluck('workspace_id')
        ->toArray();

    $sharedWorkspaceIds = array_intersect($adminWorkspaceIds, $staffWorkspaceIds);

    if (empty($sharedWorkspaceIds)) {
        return redirect()->back()->with('error', 'You do not share any workspace with this staff user.');
    }

    // Get allowed permission IDs based on shared workspaces
    $allowedPermissionIds = DB::table('base_permissions')
        ->whereIn('workspace_id', $sharedWorkspaceIds)
        ->pluck('id')
        ->toArray();

    // Sanitize input: keep only those permissions the admin can assign
    $newPermissions = collect($request->input('permissions', []))
        ->map(fn($id) => (int)$id)
        ->intersect($allowedPermissionIds)
        ->unique()
        ->values()
        ->all();

    // Fetch old permission state for audit log
    $oldData = DB::table('users_permissions')
        ->where('user_id', $staff->id)
        ->pluck('permission_id')
        ->toArray();

    // Remove all old permissions
    DB::table('users_permissions')->where('user_id', $staff->id)->delete();

    // Insert new scoped permissions
    if (!empty($newPermissions)) {
        $insert = [];
        foreach ($newPermissions as $permId) {
            $insert[] = [
                'user_id' => $staff->id,
                'permission_id' => $permId,
                'created_by' => $authUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users_permissions')->insert($insert);
    }

    // Log changes
    $this->logAction($request, 'update', $oldData, $newPermissions, $staff->id, 200, "Staff permissions updated successfully.");

    return redirect()->back()->with('success', 'Permissions updated successfully.');
}














    protected function logAction($request, $action, $oldData = null, $newData = null, $id = null, $status_code = null, $description = null) 
    {
        $uid = Str::random(32);
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        
    
        $userAgent      = $request->userAgent();
        $browser        = $agent->browser();
        $platform       = $agent->platform();
        $deviceType     = $agent->isMobile() ? 'mobile' : 'desktop';
        $requestHeaders = json_encode($request->headers->all());

        



        $requestIp = request()->header('X-Forwarded-For') ?: request()->ip();
        $logId = DB::table('audit_logs_general')->insertGetId([
            'uid' => $uid,
            'user_id' => auth()->id(),
            'victim_user_id' => $id,
            'action' => $action,
            'description' => $description,
            'status_code' => $status_code,
            'model' => 'UserProfile',
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $requestIp,
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'browser' => $browser,
            'platform' => $platform,
            'device_type' => $deviceType,
            'request_headers' => $requestHeaders
        ]);

        // Fetch the newly created log record to dispatch the job
        $log = DB::table('audit_logs_general')->where('id', $logId)->first();

        // Dispatch job to fetch IP intelligence (async via database queue) with table name
        dispatch(new \App\Jobs\FetchIpIntelligenceJob($log));
        
    }

    }
