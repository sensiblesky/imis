<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;


class FetchIpIntelligenceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $log;
    public $table;

    public function __construct($log, $table = 'audit_logs_general')
    {
        $this->log = $log;
        $this->table = $table;
    }

    public function handle()
    {
        $ip = trim($this->log->ip_address);

        if ($this->isPrivateIp($ip)) {
            DB::table($this->table)->where('id', $this->log->id)->update([
                'api_status' => 'skipped_local_ip',
                'updated_at' => now(),
            ]);
            return;
        }

        // Check for reused geo data
        $existingData = DB::table($this->table)
            ->where('ip_address', $ip)
            ->where('api_status', 'success')
            ->orderByDesc('id')
            ->first();

        if ($existingData) {
            DB::table($this->table)->where('id', $this->log->id)->update([
                'api_status'     => $existingData->api_status,
                'country'        => $existingData->country,
                'country_code'   => $existingData->country_code,
                'region'         => $existingData->region,
                'region_name'    => $existingData->region_name,
                'city'           => $existingData->city,
                'zip'            => $existingData->zip,
                'latitude'       => $existingData->latitude,
                'longitude'      => $existingData->longitude,
                'timezone'       => $existingData->timezone,
                'isp'            => $existingData->isp,
                'organization'   => $existingData->organization,
                'asy'            => $existingData->asy,
                'query'          => $existingData->query,
                'updated_at'     => now(),
            ]);
            return;
        }

        try {
            //generate uid using str_random
            $uid = \Illuminate\Support\Str::random(32);
            $response = Http::timeout(5)->get(env('GEO_IP_API_URL') . $ip);

            $data = $response->successful() ? $response->json() : [];

            //include uid in the data
            $data['uid'] = $uid;

            DB::table($this->table)->where('id', $this->log->id)->update([
                'api_status'     => $data['status'] ?? 'fail',
                'country'        => $data['country'] ?? null,
                'country_code'   => $data['countryCode'] ?? null,
                'region'         => $data['region'] ?? null,
                'region_name'    => $data['regionName'] ?? null,
                'city'           => $data['city'] ?? null,
                'zip'            => $data['zip'] ?? null,
                'latitude'       => $data['lat'] ?? null,
                'longitude'      => $data['lon'] ?? null,
                'timezone'       => $data['timezone'] ?? null,
                'isp'            => $data['isp'] ?? null,
                'organization'   => $data['org'] ?? null,
                'asy'            => $data['as'] ?? null,
                'query'          => $data['query'] ?? null,
                'uid'            => $uid,
                'updated_at'     => now(),
            ]);

        } catch (\Throwable $e) {
            DB::table($this->table)->where('id', $this->log->id)->update([
                'api_status' => 'fail',
                'updated_at' => now(),
            ]);





            
        }
    }

    protected function isPrivateIp($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return false;

        $privateRanges = [
            '10.', '127.', '192.168.',
            '172.16.', '172.17.', '172.18.', '172.19.', '172.20.',
            '172.21.', '172.22.', '172.23.', '172.24.', '172.25.',
            '172.26.', '172.27.', '172.28.', '172.29.', '172.30.', '172.31.',
            '::1',
        ];

        foreach ($privateRanges as $range) {
            if (str_starts_with($ip, $range)) {
                return true;
            }
        }

        return false;
    }
}

