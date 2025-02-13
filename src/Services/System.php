<?php

declare(strict_types=1);

namespace Plakhin\FpmOptimize\Services;

final class System
{
    /** @return array<string, int> */
    public function getConfigAndLoadValues(): array
    {
        return [
            'cpu_cores' => $this->getCpuCoresCount(),
            'available_ram' => $this->getAvailableRam(),
            'avg_worker_usage' => $this->getAvgWorkerUsage(),
        ];
    }

    private function getCpuCoresCount(): int
    {
        return (int) match (PHP_OS_FAMILY) {
            'Windows' => shell_exec('echo %NUMBER_OF_PROCESSORS%'),
            default => shell_exec('nproc'),
        };
    }

    private function getAvailableRam(): int
    {
        return match (true) {
            PHP_OS_FAMILY === 'Darwin'
                && $val = shell_exec('
                    vm_stat | awk \'
                    /page size of/ {page_size=$8/1024}
                    /Pages free/ {free=$3}
                    /Pages inactive/ {inactive=$3}
                    END {print (free + inactive) * page_size / 1024}\'
                ') => (int) $val,

            PHP_OS_FAMILY === 'Windows'
                && $val = shell_exec(
                    'for /f "tokens=2 delims==" %A in (\'wmic OS get FreePhysicalMemory /Value\') do @echo %A'
                ) => (int) $val / 1024,

            default => (int) shell_exec('free -m | awk \'/^Mem:/ {print $7}\''),
        };
    }

    private function getAvgWorkerUsage(): int
    {
        return match (true) {
            PHP_OS_FAMILY === 'Windows'
                && $val = (int) shell_exec(
                    'powershell -Command "$procs = Get-Process php-fpm '
                    .'-ErrorAction SilentlyContinue; if ($procs) { '
                    .'($procs | Measure-Object WorkingSet64 -Sum).Sum / '
                    .'$procs.Count / 1MB } else { 0 }"'
                ) => $val,

            PHP_OS_FAMILY !== 'Windows'
                && $val = (int) shell_exec('
                    ps aux | awk \'
                    /php-fpm: pool/ && !/awk/ {sum += $6; count++}
                    END {print (count > 0 ? sum / count / 1024 : 0)}\'
                ') => $val,

            default => (int) round(ini_parse_quantity(ini_get('memory_limit')) / 1024 / 1024)
        };
    }
}
