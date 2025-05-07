<?php

declare(strict_types=1);

use Illuminate\Process\Factory;

it('outputs current and suggested values in human readable format')
    ->skip(PHP_OS_FAMILY !== 'Linux', 'Test is only for Linux')
    ->expect((new Factory)->newPendingProcess()->run('sh suggest-fpm-config-values.sh')->output())
    ->toContain('System Information:')
    ->toContain('CPU Cores: ')
    ->toContain('Available RAM: ')
    ->toContain('Average Worker Usage: ')
    ->toContain('Optimal PHP-FPM Configuration:')
    ->toContain('pm.max_children = ')
    ->toContain('pm.start_servers = ')
    ->toContain('pm.min_spare_servers = ')
    ->toContain('pm.max_spare_servers = ');

it('outputs current and suggested values in json format')
    ->skip(PHP_OS_FAMILY !== 'Linux', 'Test is only for Linux')
    ->expect((new Factory)->newPendingProcess()->run('sh suggest-fpm-config-values.sh --json')->output())
    ->toContain('{"system":{')
    ->toContain('"cpu_cores":')
    ->toContain('"available_ram":')
    ->toContain('"avg_worker_usage":')
    ->toContain(',"optimal":{')
    ->toContain('"max_children":')
    ->toContain('"start_servers":')
    ->toContain('"min_spare_servers":')
    ->toContain('"max_spare_servers":');

it('exits with error on unsupported systems')
    ->skip(PHP_OS_FAMILY === 'Linux', 'Test is not for Linux')
    ->expect((new Factory)->newPendingProcess()->run('sh suggest-fpm-config-values.sh')->successful())
    ->toBeFalse();
