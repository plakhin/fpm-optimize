<?php

declare(strict_types=1);

namespace Plakhin\FpmOptimize\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Plakhin\FpmOptimize\Commands\SuggestFpmConfigValues;

final readonly class InjectFpmOptimizeCommandOutput
{
    public function __construct(private SuggestFpmConfigValues $artisanCommand) {}

    public function handle(CommandFinished $event): void
    {
        if (
            app()->runningInConsole()
            && config()->boolean('fpm-optimize.inject_into_artisan_optimise_command')
            && $event->command === 'optimize'
        ) {
            $this->artisanCommand->setOutputInterface($event->output)->handle();
        }
    }
}
