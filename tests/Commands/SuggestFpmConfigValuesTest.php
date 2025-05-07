<?php

declare(strict_types=1);

use Plakhin\FpmOptimize\Commands\SuggestFpmConfigValues;
use Symfony\Component\Console\Output\OutputInterface;

use function Pest\Laravel\artisan;

it('outputs suggestions', function (): void {
    artisan('optimize:php-fpm')
        ->assertSuccessful()
        ->expectsOutputToContain('pm.max_children = ')
        ->expectsOutputToContain('pm.start_servers = ')
        ->expectsOutputToContain('pm.min_spare_servers = ')
        ->expectsOutputToContain('pm.max_spare_servers = ');
})->skip(PHP_OS_FAMILY !== 'Linux', 'Test is only for Linux');

it('exits with error on unsupported systems', function (): void {
    artisan('optimize:php-fpm')
        ->assertFailed()
        ->expectsOutputToContain('Error: This command only supports Linux systems.');
})->skip(PHP_OS_FAMILY === 'Linux', 'Test is not for Linux');

it('allows to set output interface', function (): void {
    $command = new SuggestFpmConfigValues;
    $outputInterface = $this->createMock(OutputInterface::class);

    expect($command->setOutputInterface($outputInterface))->toBe($command);
});
