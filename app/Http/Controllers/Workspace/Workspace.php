<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Workspace extends Controller
{
    public function workspaceHome(Request $request)
    {
        try {
            $userId = Auth::id();

            $user = DB::table('users')
                ->select('id', 'firstname', 'lastname', 'photo', 'email', 'default_workspace')
                ->where('id', $userId)
                ->first();

            $workspaces = DB::table('workspaces')
                ->select('workspaces.id', 'name', 'display_name', 'description', 'icon', 'route_prefix', 'is_active')
                ->join('user_workspaces', 'workspaces.id', '=', 'user_workspaces.workspace_id')
                ->where('user_workspaces.user_id', $userId)
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($workspace) use ($user) {
                    $workspace->is_default = ($workspace->id == $user->default_workspace);
                    return $workspace;
                });

            return view('workspace.index', compact('user', 'workspaces'));
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load dashboard.');
        }
    }

    public function setDefault(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|integer|exists:workspaces,id',
        ]);

        $userId = Auth::id();
        $workspaceId = $request->workspace_id;

        $isAssigned = DB::table('user_workspaces')
            ->where('user_id', $userId)
            ->where('workspace_id', $workspaceId)
            ->exists();

        if (!$isAssigned) {
            return redirect()->back()->withErrors([
                'error' => 'Hah, Sorry! You are not assigned to this workspace.',
            ]);
        }

        DB::table('users')
            ->where('id', $userId)
            ->update(['default_workspace' => $workspaceId]);

        $workspaceRoute = '/' . DB::table('workspaces')
            ->where('id', $workspaceId)
            ->value('route_prefix') . '/dashboard';

        return redirect($workspaceRoute);
    }
}
