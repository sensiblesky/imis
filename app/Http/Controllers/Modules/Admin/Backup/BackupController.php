<?php

namespace App\Http\Controllers\Modules\Admin\Backup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
// use ZipArchive
use ZipArchive;

class BackupController extends Controller
{


    public function index()
{
    $logs = DB::table('base_settings_backup_logs as logs')
        ->leftJoin('users as u', 'logs.created_by', '=', 'u.id')
        ->select(
            'logs.*',
            'u.firstname',
            'u.lastname'
        )
        ->orderByDesc('logs.created_at')
        ->limit(100)
        ->get();

    $backupSettings = DB::table('base_settings_backup')->first();

    return view('modules.administrator.backup.index', compact('backupSettings', 'logs'));
}






    public function storeBackupSettings(Request $request)
    {
        $request->validate([
            'disk' => 'required|in:local,s3',
            'frequency' => 'required|in:daily,weekly,monthly',
            'type' => 'required|in:full,database,files',
            'time' => 'required|date_format:H:i',
            'status' => 'required|boolean',
        ]);

        $data = [
            'storage' => $request->disk,
            'frequency' => $request->frequency,
            'time' => $request->time,
            'enabled' => $request->status,
            'type' => $request->type,
            'updated_at' => now(),
        ];

        $exists = DB::table('base_settings_backup')->exists();

        if ($exists) {
            DB::table('base_settings_backup')->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('base_settings_backup')->insert($data);
        }

        return redirect()->back()->with('success', 'Backup settings have been saved successfully.');
    }



    


    
    
    

    
    public static function downloadBackupFile()
{
    try {
        $settings = DB::table('base_settings_backup')->where('enabled', 1)->first();

        if (!$settings) {
            return redirect()->route('admin.system.backup')->withErrors(['Backup is disabled in settings, please enable it first.']);
        }

        $uid = (string) Str::uuid();
        $disk = $settings->storage ?? 'local';
        $type = $settings->type ?? 'full';
        $createdBy = auth()->id() ?? null;
        $relativePath = '';

        $backupDir = public_path('storage/system/backups');
        Log::info("Backup directory: {$backupDir}");

        if (!File::exists($backupDir)) {
            Log::info("Backup directory does not exist, creating...");
            File::makeDirectory($backupDir, 0755, true);
        }

        if (!is_writable($backupDir)) {
            throw new \Exception("Backup directory is not writable: {$backupDir}");
        }

        $finalZipPath = "{$backupDir}/{$uid}.zip";
        Log::info("Final ZIP path: {$finalZipPath}");

        $zip = new ZipArchive();
        if ($zip->open($finalZipPath, ZipArchive::CREATE) !== true) {
            throw new \Exception("Failed to create ZIP archive at $finalZipPath");
        }

        // Database backup
        if ($type === 'database' || $type === 'full') {
            $tempDir = storage_path("app/tmp_backup_{$uid}");
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $sqlFile = "{$tempDir}/database.sql";

            $dbHost = escapeshellarg(config('database.connections.mysql.host'));
            $dbName = escapeshellarg(config('database.connections.mysql.database'));
            $dbUser = escapeshellarg(config('database.connections.mysql.username'));
            $dbPass = escapeshellarg(config('database.connections.mysql.password'));
            $escapedSqlFile = escapeshellarg($sqlFile);

            $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$escapedSqlFile}";
            exec($command, $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($sqlFile)) {
                throw new \Exception("Database dump failed (code {$returnVar})");
            }

            $zip->addFile($sqlFile, 'database.sql');
        }

        // Filesystem backup
        if ($type === 'filesystem' || $type === 'full') {
            $tempFileDir = storage_path("app/tmp_files_{$uid}");

            if (!File::exists($tempFileDir)) {
                File::makeDirectory($tempFileDir, 0755, true);
            }

            $fileDirs = [
                storage_path('app/public/uploads'),
                storage_path('app/public/documents'),
            ];

            foreach ($fileDirs as $dir) {
                if (!File::exists($dir)) continue;

                $files = File::allFiles($dir);
                foreach ($files as $file) {
                    $absolutePath = $file->getRealPath();
                    $relative = ltrim(str_replace(storage_path('app/public/'), '', $absolutePath), DIRECTORY_SEPARATOR);
                    $targetPath = "{$tempFileDir}/{$relative}";

                    File::ensureDirectoryExists(dirname($targetPath));
                    File::copy($absolutePath, $targetPath);
                }
            }

            $filesToZip = File::allFiles($tempFileDir);
            foreach ($filesToZip as $file) {
                $relative = ltrim(str_replace($tempFileDir, '', $file->getPathname()), DIRECTORY_SEPARATOR);
                $zip->addFile($file->getPathname(), 'files/' . $relative);
            }
        }

        $zip->close();

        // Cleanup
        if (isset($tempDir) && File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
        if (isset($tempFileDir) && File::exists($tempFileDir)) {
            File::deleteDirectory($tempFileDir);
        }

        clearstatcache();
        if (!File::exists($finalZipPath)) {
            throw new \Exception("ZIP file missing after creation: $finalZipPath");
        }

        $relativePath = 'system/backups/' . basename($finalZipPath);

        DB::table('base_settings_backup_logs')->insert([
            'type' => $type,
            'disk' => $disk,
            'status' => 'success',
            'message' => 'Backup completed successfully',
            'file_uid' => $uid,
            'file_path' => $relativePath,
            'created_by' => $createdBy,
            'created_at' => now(),
        ]);

        Log::info("Backup completed and saved to: $finalZipPath");

        return response()->download($finalZipPath);

    } catch (\Exception $e) {
        Log::error("Backup error: " . $e->getMessage());

        DB::table('base_settings_backup_logs')->insert([
            'type' => $type ?? 'unknown',
            'disk' => $disk ?? 'local',
            'status' => 'failed',
            'message' => $e->getMessage(),
            'file_uid' => $uid ?? null,
            'file_path' => $relativePath ?? null,
            'created_by' => $createdBy ?? null,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.system.backup')->withErrors(['Backup failed: ' . $e->getMessage()]);
    }
}




}
