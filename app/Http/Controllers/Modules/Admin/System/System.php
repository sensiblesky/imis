<?php

namespace App\Http\Controllers\Modules\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\LogService;

use Illuminate\Support\Facades\Http;


class System extends Controller
{
    public function sysinfo()
    {
        $phpVersion = phpversion();
    $laravelVersion = app()->version();
    $appEnv = config('app.env');
    $appDebug = config('app.debug') ? 'Enabled' : 'Disabled';
    $appUrl = config('app.url');
    $appLocale = config('app.locale');
    $appTimezone = config('app.timezone');
    $maxExecutionTime = ini_get('max_execution_time');
    $memoryLimit = ini_get('memory_limit');
    $postMaxSize = ini_get('post_max_size');
    $uploadMaxFilesize = ini_get('upload_max_filesize');
    $serverOS = php_uname();
    $currentTime = now()->toDateTimeString();

    // DB info
    $dbConnection = config('database.default');
    $dbHost = config('database.connections.' . $dbConnection . '.host');
    $dbName = config('database.connections.' . $dbConnection . '.database');

    $storagePath = storage_path('app/public');
    $storageUsageBytes = $this->folderSize($storagePath);
    $storageUsageMB = round($storageUsageBytes / 1024 / 1024, 2);
    $diskFreeGB = round(disk_free_space(storage_path()) / 1024 / 1024 / 1024, 2);

    return view('modules.administrator.system.info', compact(
        'phpVersion',
        'laravelVersion',
        'appEnv',
        'appDebug',
        'appUrl',
        'appLocale',
        'appTimezone',
        'maxExecutionTime',
        'memoryLimit',
        'postMaxSize',
        'uploadMaxFilesize',
        'serverOS',
        'currentTime',
        'dbConnection',
        'dbHost',
        'dbName',
        'storageUsageMB',
        'diskFreeGB',
    ));
    }

    // Recursive function to calculate folder size in bytes
    private function folderSize($dir)
    {
        $size = 0;
        foreach (File::allFiles($dir) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }


    public function index()
    {
        // Retrieve the system settings from the database
        $settings = DB::table('system_settings_basic')->first();

        // Fallback in case settings or maintenance_mode is null
        $maintenance_mode = $settings->maintenance_mode ?? false;

        // Get disk stats
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        // Format stats
        $diskTotalGB = round($diskTotal / 1073741824, 2); // bytes to GB
        $diskUsedGB = round($diskUsed / 1073741824, 2);
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 0);

        return view('modules.administrator.system.index', compact(
            'settings',
            'diskTotalGB',
            'diskUsedGB',
            'diskUsagePercentage',
            'maintenance_mode'
        ));
    }

    public function smtp()
    {
        $settings = DB::table('system_settings_smtp')->first();

        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        $diskTotalGB = round($diskTotal / 1073741824, 2);
        $diskUsedGB = round($diskUsed / 1073741824, 2);
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 0);

        return view('modules.administrator.system.smtp', compact(
            'settings',
            'diskTotalGB',
            'diskUsedGB',
            'diskUsagePercentage',
        ));
    }

    public function sms()
    {
        $settings = DB::table('system_settings_sms')->first();
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        $diskTotalGB = round($diskTotal / 1073741824, 2);
        $diskUsedGB = round($diskUsed / 1073741824, 2);
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 0);

        return view('modules.administrator.system.sms', compact(
            'settings',
            'diskTotalGB',
            'diskUsedGB',
            'diskUsagePercentage',
        ));
    }
    

    public function basic_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'nullable|string',
            'cc' => 'nullable|string',
            'developer' => 'nullable|string',
            'app_url' => 'nullable|url',
            'app_phone' => 'nullable|string',
            'app_email' => 'nullable|email',
            'app_address' => 'nullable|string',
            'logo_light' => 'nullable|image|mimes:jpeg,png,jpg|dimensions:max_width=1370,max_height=300,min_width=1370,min_height=300',
            'logo_dark' => 'nullable|image|mimes:jpeg,png,jpg|dimensions:max_width=1370,max_height=300,min_width=1370,min_height=300',
            'logo_icon' => 'nullable|image|mimes:svg',
            'maintenance_mode' => 'nullable|string|in:on', // Allow only 'on' as a valid value
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $maintenanceMode = $request->input('maintenance_mode') === 'on' ? 1 : 0;

        // Retrieve current system settings from the database
        $settings = DB::table('system_settings_basic')->first();

        // Prepare data array to update the settings
        $data = [
            'app_name' => $request->app_name,
            'cc' => $request->cc,
            'developer' => $request->developer,
            'app_url' => $request->app_url,
            'app_phone' => $request->app_phone,
            'app_email' => $request->app_email,
            'app_address' => $request->app_address,
            'maintenance_mode' => $maintenanceMode,
        ];

        // Handle logo uploads and replace existing files if necessary
        if ($request->hasFile('logo_light')) {
            // Delete the old light logo if it exists
            if ($settings && $settings->logo_light_path && File::exists(public_path($settings->logo_light_path))) {
                File::delete(public_path($settings->logo_light_path));
            }

            // Upload the new light logo
            $logoLight = $request->file('logo_light');
            $logoLightName = 'logo-light.png';
            $logoLight->move(public_path('assets/images'), $logoLightName);

            $data['logo_light_path'] = 'assets/images/logo-light.png';
        }

        if ($request->hasFile('logo_dark')) {
            // Delete the old dark logo if it exists
            if ($settings && $settings->logo_dark_path && File::exists(public_path($settings->logo_dark_path))) {
                File::delete(public_path($settings->logo_dark_path));
            }

            // Upload the new dark logo
            $logoDark = $request->file('logo_dark');
            $logoDarkName = 'logo-dark.png';
            $logoDark->move(public_path('assets/images'), $logoDarkName);

            $data['logo_dark_path'] = 'assets/images/logo-dark.png';
        }

        if ($request->hasFile('logo_icon')) {
            // Delete the old icon logo if it exists
            if ($settings && $settings->logo_icon_path && File::exists(public_path($settings->logo_icon_path))) {
                File::delete(public_path($settings->logo_icon_path));
            }

            // Upload the new icon logo
            $logoIcon = $request->file('logo_icon');
            $logoIconName = 'logo.svg';
            $logoIcon->move(public_path('assets/images'), $logoIconName);

            $data['logo_icon_path'] = 'assets/images/logo.svg';
        }

        // Before updating the database, save a log entry
        $this->logAction('updated system settings', $settings, $data);

        // Update the settings in the database
        DB::table('system_settings_basic')->update($data);

        // No need to clear or update cache (since Redis is not being used anymore)

        return redirect()->back()->with('success', 'System settings updated successfully!');
    }

    public function smtp_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mailer'        => 'required|string|max:50',
            'host'          => 'required|string',
            'port'          => 'required|integer',
            'username'      => 'required|string',
            'password'      => 'required|string',
            'encryption'    => 'nullable|string|in:ssl,tls,null',
            'from_address'  => 'required|email',
            'from_name'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $newData = [
            'mailer'        => $request->mailer,
            'host'          => $request->host,
            'port'          => $request->port,
            'username'      => $request->username,
            'password'      => $request->password,
            'encryption'    => $request->encryption ?? null,
            'from_address'  => $request->from_address,
            'from_name'     => $request->from_name,
            'updated_at'    => now(),
        ];

        $existing = DB::table('system_settings_smtp')->first();
        $oldData = $existing ? (array) $existing : null;

        if ($existing) {
            DB::table('system_settings_smtp')->update($newData);
        } else {
            $newData['created_at'] = now();
            DB::table('system_settings_smtp')->insert($newData);
        }

        $this->logAction($existing ? 'updated smtp settings' : 'created smtp settings', $oldData, $newData);

        return back()->with('success', 'SMTP settings updated successfully.');
    }

    public function sms_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_name' => 'required|string|max:100',
            'api_key'      => 'required|string',
            'sender_id'    => 'required|string|max:100',
            'base_url'     => 'required|url',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $newData = [
            'gateway_name' => $request->gateway_name,
            'api_key'      => $request->api_key,
            'sender_id'    => $request->sender_id,
            'base_url'     => $request->base_url,
            'updated_at'   => now(),
        ];

        $existing = DB::table('system_settings_sms')->first();
        $oldData = $existing ? (array) $existing : null;

        if ($existing) {
            DB::table('system_settings_sms')->update($newData);
        } else {
            $newData['created_at'] = now();
            DB::table('system_settings_sms')->insert($newData);
        }

        $this->logAction($existing ? 'updated sms settings' : 'created sms settings', $oldData, $newData);

        return back()->with('success', 'SMS settings updated successfully.');
    }

    public function CampusIndex()
    {
        $campuses = DB::table('base_campuses')->orderBy('name')->get();




        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        $diskTotalGB = round($diskTotal / 1073741824, 2);
        $diskUsedGB = round($diskUsed / 1073741824, 2);
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 0);

        return view('modules.administrator.system.campus', compact('campuses','diskTotalGB','diskUsedGB','diskUsagePercentage',));
    }
    


    public function CampusStore(Request $request)
    {
        $validated = $request->validate([
            'campus_code' => 'required|string|max:2|unique:base_campuses,uid',
            'name'   => 'required|string|max:100|unique:base_campuses,name',
            'status' => 'required|in:0,1',
        ]);

        // Prepare the data for the new campus entry
        $newData = [
            'uid'    => (string) Str::uuid(),
            'campus_code' => $validated['campus_code'],
            'name'   => $validated['name'],
            'status' => $validated['status'],
        ];

        // Since this is a new record, there won't be any old data to log.
        $oldData = null;

        // Insert new campus data into the campuses table
        DB::table('base_campuses')->insert($newData);

        // Log the creation of a new campus
        $this->logAction('created campus', $oldData, $newData);

        return back()->with('success', 'Campus created successfully.');
    }

    public function CampusUpdate(Request $request, $uid)
    {
        // Validate the incoming request
        $request->validate([
            'campus_code' => 'required|string|max:2|unique:base_campuses,uid,' . $uid . ',uid',	
            'name'   => 'required|string|max:100|unique:base_campuses,name,' . $uid . ',uid',
            'status' => 'nullable|in:0,1',
        ]);

        // Retrieve the existing campus data to compare with the new data
        $existingCampus = DB::table('base_campuses')->where('uid', $uid)->first();

        // Prepare the new data for update
        $newData = [
            'campus_code' => $request->campus_code,
            'name'   => $request->name,
            'status' => $request->status ?? 1,  // Default to 1 if status is null
        ];

        // Before updating the database, save a log entry (compare old and new data)
        $this->logAction('updated campus', $existingCampus, $newData);

        // Update the campus data in the database
        DB::table('base_campuses')->where('uid', $uid)->update($newData);

        return back()->with('success', 'Campus Updated Successfully.');
    }

    public function CampusDestroy($uid)
    {
        // Retrieve the existing campus data before deletion
        $existingCampus = DB::table('base_campuses')->where('uid', $uid)->first();

        // Before deleting the campus, save a log entry
        $this->logAction('deleted campus', $existingCampus, null);

        // Delete the campus from the database
        DB::table('base_campuses')->where('uid', $uid)->delete();

        return back()->with('success', 'Campus deleted successfully.');
    }


    public function DepartmentIndex()
    {
        $departments = DB::table('base_departments')->orderBy('id')->get();
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        $diskTotalGB = round($diskTotal / 1073741824, 2);
        $diskUsedGB = round($diskUsed / 1073741824, 2);
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 0);

        return view('modules.administrator.system.departments', compact('departments','diskTotalGB','diskUsedGB','diskUsagePercentage',));
    }

    public function DepartmentStore(Request $request)
    {
        $validated = $request->validate([
            'department_description' => 'required|string|max:200|unique:base_departments,uid',
            'name'   => 'required|string|max:100|unique:base_departments,name',
            'status' => 'required|in:0,1',
        ]);

        // Prepare the data for the new campus entry
        $newData = [
            'uid'    => (string) Str::uuid(),
            'description' => $validated['department_description'],
            'name'   => $validated['name'],
            'status' => $validated['status'],
        ];

        // Since this is a new record, there won't be any old data to log.
        $oldData = null;

        // Insert new campus data into the campuses table
        DB::table('base_departments')->insert($newData);

        // Log the creation of a new campus
        $this->logAction('created department', $oldData, $newData);

        return back()->with('success', 'Department created successfully.');
    }

    public function DepartmentUpdate(Request $request, $uid)
    {
        // Validate the incoming request
        $request->validate([
            'department_description' => 'required|string|max:200|unique:base_departments,uid,' . $uid . ',uid',	
            'name'   => 'required|string|max:100|unique:base_departments,name,' . $uid . ',uid',
            'status' => 'nullable|in:0,1',
        ]);

        // Retrieve the existing campus data to compare with the new data
        $existingDepartment = DB::table('base_campuses')->where('uid', $uid)->first();

        // Prepare the new data for update
        $newData = [
            'description' => $request->department_description,
            'name'   => $request->name,
            'status' => $request->status ?? 1,  // Default to 1 if status is null
        ];

        // Before updating the database, save a log entry (compare old and new data)
        $this->logAction('updated department', $existingDepartment, $newData);

        // Update the campus data in the database
        DB::table('base_departments')->where('uid', $uid)->update($newData);

        return back()->with('success', 'Cepartment Updated Successfully.');
    }

    public function DepartmentDestroy($uid)
    {
        // Retrieve the existing campus data before deletion
        $existingDepartment = DB::table('base_departments')->where('uid', $uid)->first();

        // Before deleting the campus, save a log entry
        $this->logAction('deleted department', $existingDepartment, null);

        // Delete the campus from the database
        DB::table('base_departments')->where('uid', $uid)->delete();

        return back()->with('success', 'Department deleted successfully.');
    }




    public function Optmization()
    {

        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        $diskTotalGB = round($diskTotal / 1073741824, 2);
        $diskUsedGB = round($diskUsed / 1073741824, 2);
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 0);

        return view('modules.administrator.system.optimize', compact('diskTotalGB','diskUsedGB','diskUsagePercentage',));
    }

    


    public function optimizeCommand(Request $request)
    {
        // Run optimize
        Artisan::call('optimize');
        $optimizeOutput = Artisan::output();

        // Run cache:clear
        Artisan::call('cache:clear');
        $cacheClearOutput = Artisan::output();

        // Combine outputs
        $combinedOutput = ">>> php artisan optimize\n\n" . $optimizeOutput .
                        "\n\n>>> php artisan cache:clear\n\n" . $cacheClearOutput;

        return response()->json([
            'status' => 'success',
            'message' => $combinedOutput,
        ]);
    }





    protected function logAction($action, $oldData = null, $newData = null)
    {
        //generate uid using str_random
        $uid = Str::random(32);
        $requestIp = request()->header('X-Forwarded-For') ?: request()->ip();
        $logId = DB::table('audit_logs_general')->insertGetId([
            'uid' => $uid,
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => 'SystemSettings',
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $requestIp,
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        // Fetch the newly created log record to dispatch the job
        $log = DB::table('audit_logs_general')->where('id', $logId)->first();

        // Dispatch job to fetch IP intelligence (async via database queue)

        dispatch(new \App\Jobs\FetchIpIntelligenceJob($log, 'audit_logs_general'));        
    }



    public function jobCenter()
    {
        $jobs = DB::table('jobs')->orderByDesc('id')->limit(50)->get();
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        $diskTotalGB = round($diskTotal / 1073741824, 2);
        $diskUsedGB = round($diskUsed / 1073741824, 2);
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 0);

        return view('modules.administrator.system.jobs', compact(
            'diskTotalGB',
            'diskUsedGB',
            'diskUsagePercentage',
        ));

    }




    public function runJob(Request $request)
    {
        $jobType = $request->input('job_type');
        $logId = $request->input('log_id');

        $log = DB::table('audit_logs_general')->where('id', $logId)->first();

        if (!$log) {
            return back()->with('error', 'Log ID not found.');
        }

        switch ($jobType) {
            case 'FetchIpIntelligenceJob':
                dispatch(new \App\Jobs\FetchIpIntelligenceJob($log, 'audit_logs_general'));
                break;

            default:
                return back()->with('error', 'Unknown job type selected.');
        }

        return back()->with('success', "$jobType dispatched for log ID $logId.");
    }
}
