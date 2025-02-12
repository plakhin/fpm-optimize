<?php

declare(strict_types=1);

namespace Plakhin\FpmOptimize\Services;

final readonly class Calculate
{
    public function __construct(
        private int $cpuCores,
        private int $availableRam,
        private int $avgWorkerUsage,
    ) {}

    /** @return array<string, int> */
    public function optimalValues(): array
    {
        $reserveRam = round($this->availableRam * 0.1);

        $maxChildren = (int) floor(($this->availableRam - $reserveRam) / $this->avgWorkerUsage / 10) * 10;
        $startServers = (int) min(round($maxChildren * 0.25), $this->cpuCores * 4);
        $minSpareServers = (int) min(round($maxChildren * 0.25), $this->cpuCores * 2);
        $maxSpareServers = (int) min(round($maxChildren * 0.75), $this->cpuCores * 4);

        return [
            'max_children' => $maxChildren,
            'start_servers' => $startServers,
            'min_spare_servers' => $minSpareServers,
            'max_spare_servers' => $maxSpareServers,
        ];
    }
}
