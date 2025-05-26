<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Str;
use App\Jobs\SendMailJob;
use App\Mail\LoginAlertMail;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            
            return redirect()->intended('/welcome');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Get the user first to check their status
        $user = User::where('username', $credentials['username'])->first();

        if ($user && !$user->isActive()) {
            // Log failed login attempt due to inactive account
            $this->logLoginAttempt($request, 'failed', $user->id, 'Account is not active', 'login');
            return back()->withErrors([
                'username' => 'Your account is not active. Current status: ' . $user->status,
            ])->onlyInput('username');
        }

        if (Auth::attempt($credentials)) {
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());

            $platform       = $agent->platform();
            $deviceType     = $agent->isMobile() ? 'mobile' : 'desktop';

            $request->session()->regenerate();

            // Log successful login attempt
            $this->logLoginAttempt($request, 'success', Auth::id(), 'Login Success', 'login');
            //check user which has been logged in
            
            // Clear any cached data
            Cache::forget('user_' . Auth::id() . '_workspaces');
            
            // Prepare login alert data
           

            dispatch(new SendMailJob(
                $user,
                new LoginAlertMail([
                    'time' => now()->format('H:i:s'),
                    'ip' => $request->ip(),
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'platform' => $platform,
                    'device' => $deviceType,
                ])
            ));

            
            // Redirect the user to their default workspace dashboard
            return $this->redirectToWorkspace(Auth::user());
        }


        

        

        // Log failed login attempt
        // $this->logLoginAttempt($request, 'failed', null, 'Invalid credentials');
        if (!$user) {
            $this->logLoginAttempt($request, 'failed', null, 'Username not found', 'login');
        }
        else {
            $this->logLoginAttempt($request, 'failed', $user->id, 'Invalid credentials', 'login');
        }        

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    protected function redirectToWorkspace(User $user)
    {
        if ($user->default_workspace) {
            // Check if user is assigned to this workspace
            $isAssigned = DB::table('user_workspaces')
                ->where('user_id', $user->id)
                ->where('workspace_id', $user->default_workspace)
                ->exists();

            if ($isAssigned) {
                // Fetch workspace info
                $workspace = DB::table('workspaces')
                    ->where('id', $user->default_workspace)
                    ->where('is_active', 1)
                    ->first();

                if ($workspace) {
                    // Redirect to workspace's dashboard
                    return redirect("/{$workspace->route_prefix}/dashboard");
                }
            }
        }

        // Fallback if no valid default workspace
        return redirect('/welcome');
    }



    


    public function logout(Request $request)
{
    // Log logout attempt with status "success" and source "logout"
    $this->logLoginAttempt($request, 'success', Auth::id(), 'Logout Success', 'logout');

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}




    public function logoutGet(Request $request)
    {
        return $this->logout($request);
    }

    /**
     * Log login attempt in the database.
     */
    protected function logLoginAttempt(
        Request $request,
        string $status = 'failed',
        ?int $userId = null,
        ?string $note = null,
        ?string $action = null
    ) {
        $uid = Str::random(32);
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
    
        $ipAddress      = $request->header('X-Forwarded-For') ?: $request->ip();
        $userAgent      = $request->userAgent();
        $username       = $action !== 'logout' ? $request->input('username') : null;
        $password       = $status === 'failed' && $action !== 'logout' ? Str::limit($request->input('password'), 50) : null;
        $browser        = $agent->browser();
        $platform       = $agent->platform();
        $deviceType     = $agent->isMobile() ? 'mobile' : 'desktop';
        $requestHeaders = json_encode($request->headers->all());
    
        $logId = DB::table('audit_logs_login_attempts')->insertGetId([
            'uid'             => $uid,
            'user_id'         => $userId,
            'username'        => $username,
            'password'        => $password,
            'status'          => $status,
            'ip_address'      => $ipAddress,
            'user_agent'      => $userAgent,
            'browser'         => $browser,
            'platform'        => $platform,
            'device_type'     => $deviceType,
            'request_headers' => $requestHeaders,
            'action'          => $action,
            'source'          => $note,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    
        $log = DB::table('audit_logs_login_attempts')->where('id', $logId)->first();
    
        dispatch(new \App\Jobs\FetchIpIntelligenceJob($log, 'audit_logs_login_attempts'));
    }
    





}