<?php

namespace App\Http\Middleware\Workspace;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnsureUserBelongsToWorkspace
{
    public function handle(Request $request, Closure $next)
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();
    $workspacePrefix = $request->segment(1); // e.g., 'admin'

    // Find workspace by prefix and ensure it's active
    $workspace = DB::table('workspaces')
        ->where('route_prefix', $workspacePrefix)
        ->where('is_active', 1)
        ->first();

    if (!$workspace) {
        return response()->view('errors.workspace.503', [], 503);
    }

    // Check if user is assigned to this workspace
    $isAssigned = DB::table('user_workspaces')
        ->where('user_id', $user->id)
        ->where('workspace_id', $workspace->id)
        ->exists();

    // Check if the workspace is the user's default
    $isDefault = $user->default_workspace == $workspace->id;

   //manual added
        $isStudentRole = $user->role_id == '2';
        $isStudentWorkspace = $workspacePrefix === 'student'; // or whatever your student prefix is
        
        if ($isStudentRole && $isStudentWorkspace) {
            return $next($request);
        }
   //end here


    if (!$isAssigned || !$isDefault) {
        // Check if user has a valid default workspace
        $defaultWorkspace = DB::table('workspaces')
            ->join('user_workspaces', 'workspaces.id', '=', 'user_workspaces.workspace_id')
            ->where('user_workspaces.user_id', $user->id)
            ->where('workspaces.id', $user->default_workspace)
            ->where('workspaces.is_active', 1)
            ->first();

        // If the default workspace is invalid, clear it
        if (!$defaultWorkspace) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['default_workspace' => null]);
        
            return redirect()->route('welcome')
                ->withErrors([
                    'error' => 'Access denied. Defaulty You do not belong to this workspace. Please select a valid workspace from here. ',
                ]);
        }
        
        

        // Redirect to user's valid default workspace dashboard
        return redirect("/{$defaultWorkspace->route_prefix}/dashboard");
    }

    return $next($request);
}

}
