<?php

declare(strict_types=1);

use Plakhin\FpmOptimize\Services\Calculate;
use Plakhin\FpmOptimize\Services\System;

it('returns calculated optimal values', function (): void {
    $systemValues = (new System)->getConfigAndLoadValues();

    $calculateService = new Calculate(
        cpuCores: $systemValues['cpu_cores'],
        availableRam: $systemValues['available_ram'],
        avgWorkerUsage: $systemValues['avg_worker_usage'],
    );

    $optimalValues = $calculateService->optimalValues();

    expect($optimalValues['max_children'])->toBeInt();
    expect($optimalValues['start_servers'])->toBeInt();
    expect($optimalValues['min_spare_servers'])->toBeInt();
    expect($optimalValues['max_spare_servers'])->toBeInt();
});
