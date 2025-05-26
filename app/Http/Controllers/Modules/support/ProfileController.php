<?php

namespace App\Http\Controllers\Modules\support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;

class ProfileController extends Controller
{
    public function UpdateAcademiYear(Request $request)
    {
        $uid = $request->input('academic_year_uid'); // <-- match the form input name
        if (!$uid) {
            return redirect()->back()->with('error', 'Invalid academic year selected.');
        }    

        $academicYears = DB::table('base_students_accademic_years')
            ->select('id', 'uid', 'year_range')
            ->where('uid', $uid)
            ->first();



        if (!$academicYears) {
            return redirect()->back()->with('error', 'Academic year not found.');
        }

        session([
            'academic_year_id' => $academicYears->id,
            'academic_year_range' => $academicYears->year_range
        ]);

        return redirect()->back()->with('success', 'Academic year updated to ' . $academicYears->year_range);

    }

    public function show()
    {
        $user = DB::table('users')
            ->join('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select(
                'users.firstname',
                'users.middlename',
                'users.lastname',
                'users.username',
                'users.email',
                'users.phone',
                'users.gender',
                'users.photo',
                'users.two_factor_status',
                'users.two_factor_method',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'workspaces.display_name as workspace_name'
            )
            ->where('users.id', Auth::id())
            ->first();

        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', Auth::id())
            ->pluck('base_campuses.name');

        $loginLogs = DB::table('audit_logs_login_attempts')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(10) // get last 10 logins
            ->get();

        

        // Default fallback image path (relative to public/)
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        // Try to load and encode user's profile photo
        if ($user && $user->photo) {

            $photoPath = ltrim($user->photo, '/'); // Just "uploads/photos/users/xxxx.png"
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $user->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $user->photo_base64 = asset($defaultImagePath);
            }

        } else {
            // No photo set
            $user->photo_base64 = asset($defaultImagePath);
        }

        $userId = Auth::id();

        $currentSessionId = session()->getId();
        $sessions = DB::table('sessions')
            ->where('user_id', $userId)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'current_session' => $session->id === $currentSessionId, // Add this key
                ];
            });


        return view('modules.support.profile.index', compact('user', 'campuses', 'loginLogs', 'sessions'));
    }



    public function edit()
    {
        $user = DB::table('users')
            ->join('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select(
                'users.id',
                'users.firstname',
                'users.middlename',
                'users.lastname',
                'users.username',
                'users.email',
                'users.phone',
                'users.gender',
                'users.status',
                'users.photo',
                'users.password',
                'users.campus_id',
                'users.default_workspace',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'workspaces.display_name as workspace_name'
            )
            ->where('users.id', Auth::id())
            ->first();

        // ✅ Campus IDs for multi-select
        $userCampuses = DB::table('user_campuses')
            ->where('user_id', Auth::id())
            ->pluck('campus_id')
            ->toArray();

        // ✅ Campus names for badges
        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', Auth::id())
            ->pluck('base_campuses.name');

        // ✅ All campuses for the select2
        $allCampuses = DB::table('base_campuses')->get();

        $loginLogs = DB::table('audit_logs_login_attempts')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();


        $allWorkspaces = DB::table('workspaces')->where('is_active', 1)->get();
        


        // Default fallback image path (relative to public/)
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        // Try to load and encode user's profile photo
        if ($user && $user->photo) {

            $photoPath = ltrim($user->photo, '/'); // Just "uploads/photos/users/xxxx.png"
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $user->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $user->photo_base64 = asset($defaultImagePath);
            }

        } else {
            // No photo set
            $user->photo_base64 = asset($defaultImagePath);
        }


        return view('modules.support.profile.edit', compact('user', 'userCampuses', 'campuses', 'allCampuses', 'loginLogs', 'allWorkspaces'));
    }



    

    public function update(Request $request)
    {
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:M,F',
            'status' => 'nullable|in:active,inactive',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|string|same:password',
            'base_campuses.*' => 'exists:base_campuses,id',
            'default_campus' => 'nullable|exists:base_campuses,id',
            'default_workspace' => 'nullable|exists:workspaces,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get current user data for logging old data
        $oldUser = DB::table('users')->where('id', $userId)->first();
        $oldData = [
            'username' => $oldUser->username,
            'firstname' => $oldUser->firstname,
            'middlename' => $oldUser->middlename,
            'lastname' => $oldUser->lastname,
            'email' => $oldUser->email,
            'phone' => $oldUser->phone,
            'gender' => $oldUser->gender,
            'status' => $oldUser->status,
            'photo' => $oldUser->photo,
            'default_workspace' => $oldUser->default_workspace,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = FileUploadService::upload($request->file('photo'), 'profile_photo', $oldUser->photo);
        } else {
            $photoPath = $oldUser->photo;
        }

        // Prepare new data for updating user
        $newData = [
            'username' => $request->username,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'status' => $request->status,
            'photo' => $photoPath,
        ];

        // Update password if provided
        if (!empty($request->password)) {
            $newData['password'] = Hash::make($request->password);
        }

        // Log the update action (user profile update)
        // $this->logAction('updated user profile', $oldData, $newData);
        $this->logAction(request(), 'updated profile', $oldData, $newData);


        // Update user
        DB::table('users')->where('id', $userId)->update($newData);

        

        // Update default campus
        if ($request->has('default_campus')) {
            DB::table('users')->where('id', $userId)->update(['campus_id' => $request->default_campus]);
        }

       
        // Update default workspace
        if ($request->has('default_workspace')) {
            DB::table('users')->where('id', $userId)->update(['default_workspace' => $request->default_workspace]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }





    protected function logAction($request, $action, $oldData = null, $newData = null)
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
            'action' => $action,
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

        // Dispatch job to fetch IP intelligence (async via database queue)
        dispatch(new \App\Jobs\FetchIpIntelligenceJob($log));
        
    }

    public function terminateSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        $sessionId = $request->input('session_id');
        $userId = auth()->id();

        // Ensure the session belongs to the logged-in user
        $session = DB::table('sessions')->where('id', $sessionId)->where('user_id', $userId)->first();

        if (!$session) {
            return back()->with('error', 'Invalid session or unauthorized.');
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Session terminated successfully.');
    }
}
