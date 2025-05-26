<?php

namespace App\Services;

class SystemMonitorService
{
    public function getUsage(): array
    {
        // Get total and used memory
        $memoryInfo = shell_exec("free -m");
        preg_match('/Mem:\s+(\d+)\s+(\d+)/', $memoryInfo, $matches);
        $totalMemory = $matches[1] ?? 0; // Total memory in MB
        $usedMemory = $matches[2] ?? 0;  // Used memory in MB

        // Calculate the percentage of used RAM for the whole system
        $ramPercent = 0;
        if ($totalMemory > 0) {
            $ramPercent = round(($usedMemory / $totalMemory) * 100, 2); // Calculate percentage
        }

        // Get the CPU usage for the entire system (not limited to the Laravel process)
        $cpuStats = shell_exec("top -bn1 | grep 'Cpu(s)'");
        preg_match_all('/(\d+\.\d+|\d+)\s*id/', $cpuStats, $matches);
        $idleCpu = $matches[1][0] ?? 100;
        $cpuUsage = 100 - floatval($idleCpu); // CPU usage is 100% - idle CPU

        // Get system-wide CPU usage (total CPU usage)
        $coreCpu = shell_exec("mpstat -P ALL 1 1 | grep -E '^[0-9]'");
        $cores = [];
        $totalCpuUsage = 0;
        $coreCount = 0;

        foreach (explode("\n", trim($coreCpu)) as $line) {
            $parts = preg_split('/\s+/', $line);
            if (isset($parts[2])) {
                $coreId = $parts[2];
                $idle = $parts[11] ?? 100;
                $usage = 100 - floatval($idle);
                $cores[] = [
                    'core' => (int)$coreId,
                    'cpu_usage_percent' => round($usage, 2),
                ];

                // Add up the CPU usage for all cores to calculate the average
                $totalCpuUsage += $usage;
                $coreCount++;
            }
        }

        // Calculate the average CPU usage across all cores
        $averageCpuUsage = ($coreCount > 0) ? round($totalCpuUsage / $coreCount, 2) : 0;

        return [
            'total_cpu_percent' => round($cpuUsage, 2), // Total CPU usage for the system
            'total_ram_percent' => $ramPercent,          // RAM usage percentage for the system
            'total_memory_mb' => (int)$totalMemory,     // Total memory in MB
            'used_memory_mb' => (int)$usedMemory,       // Used memory in MB
            'per_core_usage' => $cores,                 // CPU usage per core
        ];
    }
}
