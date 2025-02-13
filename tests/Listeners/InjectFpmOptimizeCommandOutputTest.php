<?php

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Plakhin\FpmOptimize\Commands\SuggestFpmConfigValues;
use Plakhin\FpmOptimize\Listeners\InjectFpmOptimizeCommandOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

test('the listener is attached to the event', function (): void {
    Event::fake([CommandFinished::class]);
    Event::assertListening(CommandFinished::class, InjectFpmOptimizeCommandOutput::class);
});

test('the listener calls `SuggestFpmConfigValues::exec()` when needed', function (bool $shouldInject): void {
    $this->mock(SuggestFpmConfigValues::class, function ($mock) use ($shouldInject): void {
        $mock->shouldReceive('exec')->times((int) $shouldInject);
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
