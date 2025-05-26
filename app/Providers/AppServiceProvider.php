<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\LogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LogService::class, function ($app) {
            return new LogService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = app(Schedule::class);

            $setting = DB::table('base_settings_backup')->where('enabled', true)->first();
            if ($setting) {
                $cmd = match ($setting->type) {
                    'database' => 'backup:run --only-db',
                    'files' => 'backup:run --only-files',
                    default => 'backup:run',
                };

                $command = $schedule->command($cmd);

                switch ($setting->frequency) {
                    case 'weekly':
                        $command->weekly()->at($setting->time);
                        break;
                    case 'daily':
                    default:
                        $command->daily()->at($setting->time);
                        break;
                }

                // Track result
                $command->onSuccess(function () use ($setting) {
                    DB::table('base_settings_backup_logs')->insert([
                        'type' => $setting->type,
                        'disk' => env('BACKUP_DISK', 'local'),
                        'status' => 'success',
                        'message' => 'Backup completed successfully',
                        'created_at' => now(),
                    ]);
                })->onFailure(function ($error) use ($setting) {
                    DB::table('base_settings_backup_logs')->insert([
                        'type' => $setting->type,
                        'disk' => env('BACKUP_DISK', 'local'),
                        'status' => 'failed',
                        'message' => $error,
                        'created_at' => now(),
                    ]);
                });
            }
        });

        View::composer('*', function ($view) {
            // Cache system settings for 1 day (you can adjust the duration)
            $systemSettings = Cache::remember('system_settings_basic', now()->addDay(), function () {
                return DB::table('system_settings_basic')
                    ->select('app_name', 'cc', 'developer')
                    ->first();
            });

            if ($systemSettings) {
                $view->with('systemSettings', $systemSettings);
            }

            // Optional: Also share authenticated user photo and name
            $user = Auth::user();
            $defaultImagePath = 'assets/images/users/avatar-1.jpg';
            $photoBase64 = asset($defaultImagePath);

            if ($user) {
                if ($user->photo) {
                    $photoPath = ltrim($user->photo, '/');
                    if (Storage::disk('public')->exists($photoPath)) {
                        $fileContents = Storage::disk('public')->get($photoPath);
                        $mimeType = Storage::disk('public')->mimeType($photoPath);
                        $photoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                    }
                }

                $view->with('authUserName', $user->firstname . ' ' . $user->lastname);
                $view->with('authUserPhoto', $photoBase64);


                // Fetch user campuses
                $userCampusIdsForHeader = DB::table('user_campuses')
                    ->where('user_id', $user->id)
                    ->pluck('campus_id')
                    ->toArray();
                if ($user->campus_id) $userCampusIdsForHeader[] = $user->campus_id;

                // Fetch user workspaces
                $userWorkspaceIdsForHeader = DB::table('user_workspaces')
                    ->where('user_id', $user->id)
                    ->pluck('workspace_id')
                    ->toArray();
                if ($user->default_workspace) $userWorkspaceIdsForHeader[] = $user->default_workspace;

                // Make campuses and workspaces unique
                $userCampusIdsForHeader = array_unique($userCampusIdsForHeader);
                $userWorkspaceIdsForHeader = array_unique($userWorkspaceIdsForHeader);

                // Fetch active, non-expired, unread notifications scoped to user's campuses/workspaces
                $headerNotifications = DB::table('messages_notifications as n')
                    ->leftJoin('messages_notifications_campus as nc', 'n.id', '=', 'nc.notification_id')
                    ->leftJoin('messages_notifications_workspace as nw', 'n.id', '=', 'nw.notification_id')
                    ->leftJoin('messages_notifications_user_status as nus', function ($join) use ($user) {
                        $join->on('n.id', '=', 'nus.notification_id')
                            ->where('nus.user_id', '=', $user->id)
                            ->where('nus.is_read', '=', 1); // Already read
                    })
                    ->whereNull('nus.id') // Exclude notifications already read
                    ->where(function ($query) use ($userCampusIdsForHeader, $userWorkspaceIdsForHeader) {
                        $query->whereIn('nc.campus_id', $userCampusIdsForHeader)
                            ->orWhereIn('nw.workspace_id', $userWorkspaceIdsForHeader);
                    })
                    ->where(function ($query) {
                        $query->whereNull('n.expires_at')
                            ->orWhere('n.expires_at', '>', now());
                    })
                    ->select('n.id', 'n.uid', 'n.title', 'n.message', 'n.type', 'n.created_at', 'n.created_by')
                    ->groupBy('n.id','n.uid', 'n.title', 'n.message', 'n.type', 'n.created_at', 'n.created_by')
                    ->orderBy('n.created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($notification) {
                        // Fetch the user who created the notification
                        $createdBy = DB::table('users')->find($notification->created_by);

                        // Default avatar
                        $userPhotoBase64 = asset('assets/images/users/avatar-1.jpg');

                        // Get base64 user photo if available
                        if ($createdBy && $createdBy->photo) {
                            $photoPath = ltrim($createdBy->photo, '/');
                            if (Storage::disk('public')->exists($photoPath)) {
                                $fileContents = Storage::disk('public')->get($photoPath);
                                $mimeType = Storage::disk('public')->mimeType($photoPath);
                                $userPhotoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                            }
                        }

                        // Add creator info to notification
                        $notification->user_photo = $userPhotoBase64;
                        $notification->user_firstname = $createdBy->firstname ?? '';
                        $notification->user_lastname = $createdBy->lastname ?? '';

                        return $notification;
                    });

                // Pass the result to the view
                $view->with('header_notifications', $headerNotifications);
            }
        });
    }
}
