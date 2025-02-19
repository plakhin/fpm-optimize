<?php

declare(strict_types=1);

namespace Plakhin\FpmOptimize\Commands;

use Illuminate\Console\Command;
use Plakhin\FpmOptimize\Services\Calculate;
use Plakhin\FpmOptimize\Services\System;
use Symfony\Component\Console\Output\OutputInterface;

class SuggestFpmConfigValues extends Command
{
    protected $signature = 'optimize:php-fpm';

    protected $description = 'Suggests optimal php-fpm config values based on system config and load';

    private ?OutputInterface $outputInterface = null;

    public function setOutputInterface(?OutputInterface $outputInterface = null): self
    {
        $this->outputInterface = $outputInterface;

        return $this;
    }

    public function handle(): void
    {
        $output = $this->outputInterface ?? $this->output->getOutput();

        $systemValues = app(System::class)->getConfigAndLoadValues();

        $calculateService = app()->makeWith(Calculate::class, [
            'cpuCores' => $systemValues['cpu_cores'],
            'availableRam' => $systemValues['available_ram'],
            'avgWorkerUsage' => $systemValues['avg_worker_usage'],

        ]);

        $optimalValues = $calculateService->optimalValues();

        $output->writeln('');
        $output->write('  <fg=white;bg=blue> INFO </> ');
        $output->writeln("CPU Cores: {$systemValues['cpu_cores']}, "
            ."Available RAM: {$systemValues['available_ram']} MB, "
            ."Average PHP-FPM worker usage: {$systemValues['avg_worker_usage']} MB");
        $output->writeln('');
        $output->writeln('  The [fpm-optimize] package suggests the next PHP-FPM pool config values:');
        $output->writeln('  <info>pm = dynamic</>');
        $output->writeln("  <info>pm.max_children = {$optimalValues['max_children']}</>");
        $output->writeln("  <info>pm.start_servers = {$optimalValues['start_servers']}</>");
        $output->writeln("  <info>pm.min_spare_servers = {$optimalValues['min_spare_servers']}</>");
        $output->writeln("  <info>pm.max_spare_servers = {$optimalValues['max_spare_servers']}</>");

        $output->writeln('');
        $output->write('  <fg=black;bg=yellow> WARN </> ');
        $output->writeln('Please review all the values carefully before adjusting the config!');
        $output->writeln('');
    }
}
