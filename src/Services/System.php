<?php

declare(strict_types=1);

namespace Plakhin\FpmOptimize\Services;

use Illuminate\Process\Factory;

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

    private function exec(string $command): int
    {
        $process = (new Factory)->newPendingProcess();
        $result = $process->run($command);

        return (int) mb_trim((string) $result->output());
    }

    private function getCpuCoresCount(): int
    {
        return max(1, match (PHP_OS_FAMILY) {
            'Windows' => $this->exec('echo %NUMBER_OF_PROCESSORS%'),
            default => $this->exec('nproc'),
        });
    }

    private function getAvailableRam(): int
    {
        return max(1, match (true) {
            PHP_OS_FAMILY === 'Darwin'
                && $val = $this->exec('
                    vm_stat | awk \'
                    /page size of/ {page_size=$8/1024}
                    /Pages free/ {free=$3}
                    /Pages inactive/ {inactive=$3}
                    END {print (free + inactive) * page_size / 1024}\'
                ') => (int) $val,

            PHP_OS_FAMILY === 'Windows'
                && $val = $this->exec(
                    'for /f "tokens=2 delims==" %A in (\'wmic OS get FreePhysicalMemory /Value\') do @echo %A'
                ) => (int) ceil($val / 1024),

            default => $this->exec('free -m | awk \'/^Mem:/ {print $7}\''),
        });
    }

    private function getAvgWorkerUsage(): int
    {
        return max(1, match (true) {
            PHP_OS_FAMILY === 'Windows'
                && $val = $this->exec(
                    'powershell -Command "$procs = Get-Process php-fpm '
                    .'-ErrorAction SilentlyContinue; if ($procs) { '
                    .'($procs | Measure-Object WorkingSet64 -Sum).Sum / '
                    .'$procs.Count / 1MB } else { 0 }"'
                ) => (int) $val,

            PHP_OS_FAMILY !== 'Windows'
                && $val = $this->exec('
                    ps aux | awk \'
                    /php-fpm: pool/ && !/awk/ {sum += $6; count++}
                    END {print (count > 0 ? sum / count / 1024 : 0)}\'
                ') => (int) $val,

            default => (int) round(ini_parse_quantity(ini_get('memory_limit')) / 1024 / 1024)
        });
    }
}
