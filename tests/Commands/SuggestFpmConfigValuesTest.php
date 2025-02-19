<?php

use function Pest\Laravel\artisan;

it('outputs suggestions', function (): void {
    artisan('optimize:php-fpm')
        ->assertSuccessful()
        ->expectsOutputToContain('pm.max_children = ')
        ->expectsOutputToContain('pm.start_servers = ')
        ->expectsOutputToContain('pm.min_spare_servers = ')
        ->expectsOutputToContain('pm.max_spare_servers = ');
});
