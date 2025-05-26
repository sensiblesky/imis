<?php

namespace App\Http\Controllers\Modules\Admin\Audi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class Audi extends Controller
{
    public function AuthenticationLogs()
    {
        $query = DB::table('audit_logs_login_attempts')
            ->select(
                'id',
                'uid',
                'username',
                'status',
                'ip_address',
                'browser',
                'platform',
                'city',
                'country',
                'device_type',
                'user_agent',
                'action',
                'created_at',
            );

        if (request('username')) {
            $query->where('username', 'like', '%' . request('username') . '%');
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('action')) {
            $query->where('action', request('action'));
        }

        if (request('ip_address')) {
            $query->where('ip_address', 'like', '%' . request('ip_address') . '%');
        }

        if (request('user_agent')) {
            $query->where('user_agent', 'like', '%' . request('user_agent') . '%');
        }

        if (request('browser')) {
            $query->where('browser', request('browser'));
        }

        if (request('platform')) {
            $query->where('platform', request('platform'));
        }

        if (request('device_type')) {
            $query->where('device_type', request('device_type'));
        }

        // Optional: add date range filter (if you extend the form)
        if (request('start_date') && request('end_date')) {
            $query->whereBetween('audit_logs_login_attempts.created_at', [
                request('start_date'), request('end_date')
            ]);
        }
        

        $logs = $query->orderByDesc('created_at')->take(100)->get();

        return view('modules.administrator.audi.auth.index', compact('logs'));
    }



    public function AuthenticationLogsView($uid)
    {
        // Fetch the log entry by UID
        $log = DB::table('audit_logs_login_attempts')->where('uid', $uid)->first();

        if (!$log) {
            return redirect()->back()->with('error', 'Log not found.');
        }

        // Default user info if not found
        $user = null;
        $userNotFound = false;

        if ($log->user_id) {
            $user = DB::table('users')
                ->select('uid', 'firstname', 'middlename', 'lastname', 'photo', 'role_id')
                ->where('id', $log->user_id)
                ->first();
        
            if ($user) {
                if ($user->photo) {
                    $photoPath = ltrim($user->photo, '/'); // Ensure no leading slash
                    if (Storage::disk('public')->exists($photoPath)) {
                        $fileContents = Storage::disk('public')->get($photoPath);
                        $mimeType = Storage::disk('public')->mimeType($photoPath);
                        $user->photo = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                    } else {
                        $user->photo = null;
                    }
                } else {
                    $user->photo = null;
                }
            } else {
                $userNotFound = true;
            }
        } else {
            $userNotFound = true;
        }

        return view('modules.administrator.audi.auth.view', compact('log', 'user', 'userNotFound'));
    }



    public function GeneralLogs()
    {
        $query = DB::table('audit_logs_general')
            ->leftJoin('users as users_actor', 'audit_logs_general.user_id', '=', 'users_actor.id')
            ->leftJoin('users as users_victim', 'audit_logs_general.victim_user_id', '=', 'users_victim.id')
            ->select(
                'audit_logs_general.id',
                'audit_logs_general.uid',
                'audit_logs_general.action',
                'audit_logs_general.model',
                'audit_logs_general.user_id',
                'users_actor.username as actor_username',
                'audit_logs_general.victim_user_id',
                'users_victim.username as victim_username',
                'audit_logs_general.ip_address',
                'audit_logs_general.browser',
                'audit_logs_general.platform',
                'audit_logs_general.city',
                'audit_logs_general.country',
                'audit_logs_general.device_type',
                'audit_logs_general.user_agent',
                'audit_logs_general.created_at',
            );

        // Search by actor username
        if ($actorUsername = request('actor_username')) {
            $actorId = DB::table('users')->where('username', $actorUsername)->value('id');
            if ($actorId) {
                $query->where('audit_logs_general.user_id', $actorId);
            } else {
                $query->whereRaw('1=0'); // No match, force empty result
            }
        }



        // Search by victim username
        if ($victimUsername = request('victim_username')) {
            $victimId = DB::table('users')->where('username', $victimUsername)->value('id');
            if ($victimId) {
                $query->where('audit_logs_general.victim_user_id', $victimId);
            } else {
                $query->whereRaw('1=0'); // No match, force empty result
            }
        }

        // Additional filters
        if (request('action')) {
            $query->where('action', request('action'));
        }

        if (request('ip_address')) {
            $query->where('ip_address', 'like', '%' . request('ip_address') . '%');
        }

        if (request('user_agent')) {
            $query->where('user_agent', 'like', '%' . request('user_agent') . '%');
        }

        if (request('browser')) {
            $query->where('browser', request('browser'));
        }

        if (request('platform')) {
            $query->where('platform', request('platform'));
        }

        if (request('device_type')) {
            $query->where('device_type', request('device_type'));
        }

        if (request('start_date') && request('end_date')) {
            $query->whereBetween('audit_logs_general.created_at', [
                request('start_date'), request('end_date')
            ]);
        }

        $logs = $query->orderByDesc('audit_logs_general.created_at')->take(100)->get();

        return view('modules.administrator.audi.gen.index', compact('logs'));
    }





    public function GeneralLogsView($uid)
    {
        // Fetch the log entry by UID
        $log = DB::table('audit_logs_general')->where('uid', $uid)->first();

        if (!$log) {
            return redirect()->back()->with('error', 'Log not found.');
        }

        // Default user info if not found
        $user = null;
        $userNotFound = false;

        if ($log->user_id) {
            $user = DB::table('users')
                ->select('uid', 'firstname', 'middlename', 'lastname', 'photo', 'role_id')
                ->where('id', $log->user_id)
                ->first();
        
            if ($user) {
                if ($user->photo) {
                    $photoPath = ltrim($user->photo, '/'); // Ensure no leading slash
                    if (Storage::disk('public')->exists($photoPath)) {
                        $fileContents = Storage::disk('public')->get($photoPath);
                        $mimeType = Storage::disk('public')->mimeType($photoPath);
                        $user->photo = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                    } else {
                        $user->photo = null;
                    }
                } else {
                    $user->photo = null;
                }
            } else {
                $userNotFound = true;
            }
        } else {
            $userNotFound = true;
        }

        return view('modules.administrator.audi.gen.view', compact('log', 'user', 'userNotFound'));
    }



    public function logfile()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return response()->view('admin.system.logs.view', [
                'logContent' => "Log file does not exist at: $logFile",
                'logSize' => 0
            ]);
        }

        // Optional: Limit content size for large logs (e.g., last 5,000 lines)
        $logContent = File::get($logFile);
        $logSize = File::size($logFile);

        return view('modules.administrator.audi.logfile.index', [
            'logContent' => $logContent,
            'logSize' => $logSize
        ]);
    }




    public function clearLogfile()
    {
        $logFile = storage_path('logs/laravel.log');

        try {
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }

            return redirect()->route('admin.logs.logfile')->with('success', 'Log file cleared successfully.');

        } catch (\Exception $e) {
            return redirect()->route('admin.logs.logfile')->withErrors(['Failed to clear log file: ' . $e->getMessage()]);
        }
    }

    
}
