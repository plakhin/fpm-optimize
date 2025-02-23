<?php

declare(strict_types=1);

use Plakhin\FpmOptimize\Services\System;

it('returns current config and load values', function (): void {
    $values = (new System)->getConfigAndLoadValues();

    expect($values['cpu_cores'])->toBeInt();
    expect($values['available_ram'])->toBeInt();
    expect($values['avg_worker_usage'])->toBeInt();
});
