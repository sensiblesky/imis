<?php

namespace App\Http\Middleware\Base;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $maintenance = DB::table('system_settings_basic')->value('maintenance_mode');

        if ($maintenance && !$request->is('admin*')) {
            return response()->view('errors.503');
        }

        return $next($request);
    }
}
