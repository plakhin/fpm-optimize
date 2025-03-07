<?php

declare(strict_types=1);

it('outputs current and suggested values')
    ->expect((string) shell_exec('php bin/fpm-suggest --color=never'))
    ->toContain('Current system config and load values:')
    ->toContain('CPU Cores: ')
    ->toContain('Available RAM: ')
    ->toContain('Average PHP-FPM worker usage: ')
    ->toContain('Suggested php-fpm pool config values:')
    ->toContain('pm = dynamic')
    ->toContain('pm.max_children = ')
    ->toContain('pm.start_servers = ')
    ->toContain('pm.min_spare_servers = ')
    ->toContain('pm.max_spare_servers = ');
