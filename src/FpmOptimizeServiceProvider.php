<?php

declare(strict_types=1);

namespace Plakhin\FpmOptimize;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Override;
use Plakhin\FpmOptimize\Commands\SuggestFpmConfigValues;
use Plakhin\FpmOptimize\Listeners\InjectFpmOptimizeCommandOutput;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class FpmOptimizeServiceProvider extends PackageServiceProvider
{
    #[Override]
    public function boot(): void
    {
        parent::boot();
        Event::listen(CommandFinished::class, InjectFpmOptimizeCommandOutput::class);
    }

    public function configurePackage(Package $package): void
    {
        $package->name('fpm-optimize')
            ->hasConfigFile()
            ->hasCommand(SuggestFpmConfigValues::class);
    }
}
