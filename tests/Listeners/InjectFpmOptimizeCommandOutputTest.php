<?php

declare(strict_types=1);

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Plakhin\FpmOptimize\Commands\SuggestFpmConfigValues;
use Plakhin\FpmOptimize\Listeners\InjectFpmOptimizeCommandOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

it('is attached to the event', function (): void {
    Event::fake([CommandFinished::class]);
    Event::assertListening(CommandFinished::class, InjectFpmOptimizeCommandOutput::class);
});

it('injects `SuggestFpmConfigValues` output to the artisan `optimize` command', function (bool $shouldInject): void {
    $this->mock(SuggestFpmConfigValues::class, function ($mock) use ($shouldInject): void {
        $mock->shouldReceive('setOutputInterface->handle')->times((int) $shouldInject);
    });

    config(['fpm-optimize.inject_into_artisan_optimise_command' => $shouldInject]);

    event(new CommandFinished(
        'optimize',
        $this->createMock(InputInterface::class),
        $this->createMock(OutputInterface::class),
        0
    ));

    event(new CommandFinished(
        'other-command',
        $this->createMock(InputInterface::class),
        $this->createMock(OutputInterface::class),
        0
    ));
})->with([true, false]);
