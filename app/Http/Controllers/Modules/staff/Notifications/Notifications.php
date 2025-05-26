<?php

namespace App\Http\Controllers\Modules\staff\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use App\Jobs\FetchIpIntelligenceJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class Notifications extends Controller
{
    public function getNotifications(Request $request)
    {
        $user = auth()->user();

        // Gather campus IDs: assigned + default
        $campusIds = DB::table('user_campuses')
            ->where('user_id', $user->id)
            ->pluck('campus_id')
            ->toArray();

        if ($user->campus_id) {
            $campusIds[] = $user->campus_id;
        }

        // Gather workspace IDs: assigned + default
        $workspaceIds = DB::table('user_workspaces')
            ->where('user_id', $user->id)
            ->pluck('workspace_id')
            ->toArray();

        if ($user->default_workspace) {
            $workspaceIds[] = $user->default_workspace;
        }

        // Unique filter
        $campusIds = array_unique($campusIds);
        $workspaceIds = array_unique($workspaceIds);


        // Fetch notifications
        $notifications = DB::table('messages_notifications AS n')
            ->leftJoin('messages_notifications_campus AS nc', 'n.id', '=', 'nc.notification_id')
            ->leftJoin('base_campuses AS c', 'nc.campus_id', '=', 'c.id')
            ->leftJoin('messages_notifications_workspace AS nw', 'n.id', '=', 'nw.notification_id')
            ->leftJoin('workspaces AS w', 'nw.workspace_id', '=', 'w.id')
            ->select(
                'n.id',
                'n.uid',
                'n.title',
                'n.message',
                'n.expires_at',
                'n.type',
                'n.created_at',
                DB::raw('GROUP_CONCAT(DISTINCT c.name) AS campuses'),
                DB::raw('GROUP_CONCAT(DISTINCT c.id) AS campus_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT w.name) AS workspaces'),
                DB::raw('GROUP_CONCAT(DISTINCT w.id) AS workspace_ids')
            )
            ->where(function ($query) use ($campusIds, $workspaceIds) {
                $query->where('n.is_global', 1);

                if (!empty($campusIds)) {
                    $query->orWhereIn('nc.campus_id', $campusIds);
                }

                if (!empty($workspaceIds)) {
                    $query->orWhereIn('nw.workspace_id', $workspaceIds);
                }
            })
            ->whereNotNull('n.uid')
            ->groupBy('n.id', 'n.uid','n.title', 'n.message', 'n.expires_at', 'n.type', 'n.created_at')
            ->orderByDesc('n.created_at')
            ->get();

        // Fetch campuses and workspaces the user has access to
        $campuses = DB::table('base_campuses')
            ->whereIn('id', array_unique($campusIds))
            ->where('status', '1')
            ->get();

        $workspaces = DB::table('workspaces')
            ->whereIn('id', array_unique($workspaceIds))
            ->where('is_active', '1')
            ->get();

        return view('modules.staff.notifications.index', compact('notifications', 'campuses', 'workspaces'));
    }


    public function viewNotification(Request $request, $uid)
{
    $user = auth()->user();

    if (!$uid || !$user) {
        abort(403, 'Unauthorized access.');
    }

    // Get notification ID from UID
    $notification = DB::table('messages_notifications')
        ->where('uid', $uid)
        ->first();

    if (!$notification) {
        abort(404, 'Notification not found.');
    }

    $notificationId = $notification->id;

    // Fetch user campus and workspace IDs
    $userCampusIds = DB::table('user_campuses')->where('user_id', $user->id)->pluck('campus_id')->toArray();
    if ($user->campus_id) $userCampusIds[] = $user->campus_id;

    $userWorkspaceIds = DB::table('user_workspaces')->where('user_id', $user->id)->pluck('workspace_id')->toArray();
    if ($user->default_workspace) $userWorkspaceIds[] = $user->default_workspace;

    $userCampusIds = array_unique($userCampusIds);
    $userWorkspaceIds = array_unique($userWorkspaceIds);

    // Check if user is authorized to view the notification
    $isAuthorized = DB::table('messages_notifications as n')
        ->leftJoin('messages_notifications_campus as nc', 'n.id', '=', 'nc.notification_id')
        ->leftJoin('messages_notifications_workspace as nw', 'n.id', '=', 'nw.notification_id')
        ->where('n.id', $notificationId)
        ->where(function ($query) use ($userCampusIds, $userWorkspaceIds) {
            $query->whereIn('nc.campus_id', $userCampusIds)
                ->orWhereIn('nw.workspace_id', $userWorkspaceIds);
        })
        ->exists();

    if (!$isAuthorized) {
        abort(403, 'You are not authorized to view this notification.');
    }

    // Fetch full notification with creator info
    $notification = DB::table('messages_notifications as n')
        ->leftJoin('users as u', 'n.created_by', '=', 'u.id')
        ->where('n.id', $notificationId)
        ->select(
            'n.*',
            'u.firstname as creator_firstname',
            'u.lastname as creator_lastname',
            'u.photo as creator_photo'
        )
        ->first();

    if (!$notification) {
        abort(404, 'Notification not found.');
    }

    // Convert creator's photo to base64 (fallback if not available)
    $base64Photo = asset('assets/images/users/avatar-1.jpg');
    if (!empty($notification->creator_photo)) {
        $path = ltrim($notification->creator_photo, '/');
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);
            $mime = Storage::disk('public')->mimeType($path);
            $base64Photo = 'data:' . $mime . ';base64,' . base64_encode($file);
        }
    }

    // Update notification read status
    $status = DB::table('messages_notifications_user_status')
        ->where('notification_id', $notificationId)
        ->where('user_id', $user->id)
        ->first();

    if (!$status) {
        DB::table('messages_notifications_user_status')->insert([
            'notification_id' => $notificationId,
            'user_id' => $user->id,
            'is_read' => 1,
            'read_at' => now(),
        ]);
    } elseif (!$status->is_read) {
        DB::table('messages_notifications_user_status')
            ->where('id', $status->id)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);
    }

    return view('modules.staff.notifications.view', [
        'notification' => $notification,
        'photo' => $base64Photo
    ]);
}




    public function storeNotifications(Request $request)
    {
        if ($request->isMethod('post')) {
            // Validate input
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'type' => 'required|string|in:info,warning,error,success,notice,alert, notice',
                'campuses' => 'required|array',
                'campuses.*' => 'integer|exists:base_campuses,id',
                'workspaces' => 'required|array',
                'workspaces.*' => 'integer|exists:workspaces,id',
                'expires_at' => 'nullable|date',
            ]);

            $user = auth()->user();

            // Get allowed campuses and workspaces
            $allowedCampusIds = DB::table('user_campuses')
                ->where('user_id', $user->id)
                ->pluck('campus_id')
                ->toArray();

            if ($user->campus_id) {
                $allowedCampusIds[] = $user->campus_id;
            }

            $allowedWorkspaceIds = DB::table('user_workspaces')
                ->where('user_id', $user->id)
                ->pluck('workspace_id')
                ->toArray();

            if ($user->default_workspace) {
                $allowedWorkspaceIds[] = $user->default_workspace;
            }

            $allowedCampusIds = array_unique($allowedCampusIds);
            $allowedWorkspaceIds = array_unique($allowedWorkspaceIds);

            // Detect unauthorized selections
            $unauthorizedCampuses = array_diff($validated['campuses'] ?? [], $allowedCampusIds);
            $unauthorizedWorkspaces = array_diff($validated['workspaces'] ?? [], $allowedWorkspaceIds);

            if (!empty($unauthorizedCampuses)) {
                $this->logAction(request(), 'create', null, null, null, 403, "Unauthorized campus assignment attempt on notification.");
                return redirect()->back()->withErrors([
                    'campuses' => 'You are not allowed to assign some of the selected campuses.',
                ])->withInput();
            }

            if (!empty($unauthorizedWorkspaces)) {
                $this->logAction(request(), 'create', null, null, null, 403, "Unauthorized workspace assignment attempt on notification.");
                return redirect()->back()->withErrors([
                    'workspaces' => 'You are not allowed to assign some of the selected workspaces.',
                ])->withInput();
            }

            // Proceed with insert
            $notificationId = DB::table('messages_notifications')->insertGetId([
                'title' => $validated['title'],
                'message' => $validated['body'],
                'expires_at' => $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : null,
                'type' => $validated['type'],
                'created_by' => $user->id,
                'uid' => Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($validated['campuses'])) {
                $campusData = array_map(fn($id) => [
                    'notification_id' => $notificationId,
                    'campus_id' => $id,
                ], $validated['campuses']);
                DB::table('messages_notifications_campus')->insert($campusData);
            }

            if (!empty($validated['workspaces'])) {
                $workspaceData = array_map(fn($id) => [
                    'notification_id' => $notificationId,
                    'workspace_id' => $id,
                ], $validated['workspaces']);
                DB::table('messages_notifications_workspace')->insert($workspaceData);
            }


            $newData = [
                'title' => $validated['title'],
                'message' => $validated['body'],
                'expires_at' => $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : null,
                'type' => $validated['type'],
                'created_by' => $user->id,
                'uid' => Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->logAction(request(), 'create', null, $newData, null, 200, "Created notification successfully.");

            return redirect()->back()->with('success', 'Notification created successfully.');
        }

        return redirect()->back()->withErrors('error', 'Method not allowed.');
    }


    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:messages_notifications,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
            'expires_at' => 'nullable|date',
            'campuses' => 'nullable|array',
            'campuses.*' => 'integer|exists:base_campuses,id',
            'workspaces' => 'nullable|array',
            'workspaces.*' => 'integer|exists:workspaces,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $id = $request->input('id');

        // Get allowed campuses and workspaces for the user
        $allowedCampusIds = DB::table('user_campuses')
            ->where('user_id', $user->id)
            ->pluck('campus_id')
            ->toArray();

        if ($user->campus_id) {
            $allowedCampusIds[] = $user->campus_id;
        }

        $allowedWorkspaceIds = DB::table('user_workspaces')
            ->where('user_id', $user->id)
            ->pluck('workspace_id')
            ->toArray();

        if ($user->default_workspace) {
            $allowedWorkspaceIds[] = $user->default_workspace;
        }

        $allowedCampusIds = array_unique($allowedCampusIds);
        $allowedWorkspaceIds = array_unique($allowedWorkspaceIds);

        // Check for unauthorized campus/workspace assignments
        $requestedCampusIds = $request->input('campuses', []);
        $requestedWorkspaceIds = $request->input('workspaces', []);

        $unauthorizedCampuses = array_diff($requestedCampusIds, $allowedCampusIds);
        $unauthorizedWorkspaces = array_diff($requestedWorkspaceIds, $allowedWorkspaceIds);

        if (!empty($unauthorizedCampuses)) {
            $this->logAction(request(), 'update', null, null, null, 403, "Unauthorized campus assign on update notification.");
            return back()->withErrors([
                'campuses' => 'You are not allowed to assign some of the selected campuses.',
            ])->withInput();
        }

        if (!empty($unauthorizedWorkspaces)) {
            $this->logAction(request(), 'update', null, null, null, 403, "Unauthorized workspace assign on update notification.");
            return back()->withErrors([
                'workspaces' => 'You are not allowed to assign some of the selected workspaces.',
            ])->withInput();
        }

        // Update the main notification
        DB::table('messages_notifications')->where('id', $id)->update([
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'type' => $request->input('type'),
            'expires_at' => $request->input('expires_at'),
            'updated_at' => now(),
        ]);





        $oldData = DB::table('messages_notifications')->where('id', $id)->first();
        $oldData = (array)$oldData;
        $oldData['campuses'] = DB::table('messages_notifications_campus')
            ->where('notification_id', $id)
            ->pluck('campus_id')
            ->toArray();
        $oldData['workspaces'] = DB::table('messages_notifications_workspace')
            ->where('notification_id', $id)
            ->pluck('workspace_id')
            ->toArray();




            
        // Clear old links
        DB::table('messages_notifications_campus')->where('notification_id', $id)->delete();
        DB::table('messages_notifications_workspace')->where('notification_id', $id)->delete();

        // Insert new campus links
        if (!empty($requestedCampusIds)) {
            $campusData = array_map(fn($campusId) => [
                'notification_id' => $id,
                'campus_id' => $campusId,
            ], $requestedCampusIds);

            DB::table('messages_notifications_campus')->insert($campusData);
        }

        // Insert new workspace links
        if (!empty($requestedWorkspaceIds)) {
            $workspaceData = array_map(fn($workspaceId) => [
                'notification_id' => $id,
                'workspace_id' => $workspaceId,
            ], $requestedWorkspaceIds);

            DB::table('messages_notifications_workspace')->insert($workspaceData);
        }

        ///new data for logging with all field include campus and workspace
        $newData = [
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'type' => $request->input('type'),
            'expires_at' => $request->input('expires_at'),
            'campuses' => $requestedCampusIds,
            'workspaces' => $requestedWorkspaceIds,
        ];
        
        $this->logAction(request(), 'update', $oldData, $newData, null, 200, "Updated notification successfully.");

        return back()->with('success', 'Notification updated successfully.');
    }





    public function edit($uid)
{
    $notification = DB::table('notifications')->where('uid', $uid)->first();
    $campuses = DB::table('campuses')->get();
    $workspaces = DB::table('workspaces')->get();

    if (request()->ajax()) {
        return view('modules.administrator.notifications.edit', compact('notification', 'campuses', 'workspaces'))->render();
    }

    return redirect()->route('admin.notifications.index');
}










    public function deleteNotification($uid)
{
    $user = auth()->user();

    // Get user's campuses and workspaces
    $campusIds = DB::table('user_campuses')->where('user_id', $user->id)->pluck('campus_id')->toArray();
    if ($user->campus_id) $campusIds[] = $user->campus_id;

    $workspaceIds = DB::table('user_workspaces')->where('user_id', $user->id)->pluck('workspace_id')->toArray();
    if ($user->default_workspace) $workspaceIds[] = $user->default_workspace;

    $campusIds = array_unique($campusIds);
    $workspaceIds = array_unique($workspaceIds);

    // Fetch notification
    $notification = DB::table('messages_notifications')->where('uid', $uid)->first();

    if (!$notification) {
        $this->logAction(request(), 'delete', null, null, null, 404, "Notification not found.");
        return redirect()->back()->with('error', 'Notification not found.');
    }

    // Fetch notification's assigned campuses and workspaces
    $taggedCampusIds = DB::table('messages_notifications_campus')
        ->where('notification_id', $notification->id)
        ->pluck('campus_id')
        ->toArray();

    $taggedWorkspaceIds = DB::table('messages_notifications_workspace')
        ->where('notification_id', $notification->id)
        ->pluck('workspace_id')
        ->toArray();

    // Allow if user has access to at least one campus OR one workspace
    $hasCampusAccess = count(array_intersect($taggedCampusIds, $campusIds)) > 0;
    $hasWorkspaceAccess = count(array_intersect($taggedWorkspaceIds, $workspaceIds)) > 0;

    if (!$hasCampusAccess && !$hasWorkspaceAccess) {
        $this->logAction(request(), 'delete', null, null, null, 403, "Unauthorized delete attempt on notification.");
        return redirect()->back()->with('error', 'You are not authorized to delete this notification.');
    }

    try {
        // Get old data for logging
        $oldData = (array)$notification;

        DB::table('messages_notifications')->where('id', $notification->id)->delete();

        $this->logAction(request(), 'delete', $oldData, null, null, 200, "Deleted notification successfully.");

        return redirect()->back()->with('success', 'Notification deleted successfully.');
    } catch (\Exception $e) {
        $this->logAction(request(), 'delete', null, null, null, 500, "Error deleting notification: " . $e->getMessage());
        return redirect()->back()->with('error', 'An error occurred while deleting the notification.');
    }
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
            'model' => 'Notifications',
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
