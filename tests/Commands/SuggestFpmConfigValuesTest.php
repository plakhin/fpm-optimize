<?php

use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\artisan;
use function Pest\Laravel\withoutMockingConsoleOutput;

it('outputs suggestions', function (): void {
    artisan('optimize:php-fpm')->assertSuccessful();

    withoutMockingConsoleOutput()->artisan('optimize:php-fpm');
    expect(Artisan::output())
        ->toContain('CPU Cores:')
        ->toContain('Available RAM:')
        ->toContain('Average PHP-FPM worker usage:')
        ->toContain('pm.max_children = ')
        ->toContain('pm.start_servers = ')
        ->toContain('pm.min_spare_servers = ')
        ->toContain('pm.max_spare_servers = ');
});
