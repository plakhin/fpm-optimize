<?php

declare(strict_types=1);

namespace Plakhin\FpmOptimize\Commands;

use Illuminate\Console\Command;
use Illuminate\Process\Factory;
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

        // Check if we're on Linux
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->fail('Error: This command only supports Linux systems.');
        }

        // Path to the shell script
        $scriptPath = __DIR__.'/../../suggest-fpm-config-values.sh';

        // Make sure the script is executable
        if (! is_executable($scriptPath)) {
            chmod($scriptPath, 0755);
        }

        // Run the script with JSON output flag
        $process = (new Factory)->newPendingProcess();
        $result = $process->run($scriptPath.' --json');

        if (! $result->successful()) {
            $output->writeln('<error>Error: Failed to execute the script.</error>');
            $output->writeln('<error>'.$result->errorOutput().'</error>');

            return;
        }

        // Parse the JSON output
        /** @var array{
         *      system: array{cpu_cores: int, available_ram: int, avg_worker_usage: int},
         *      optimal: array{max_children: int, start_servers: int, min_spare_servers: int, max_spare_servers: int}
         * } $data */
        $data = json_decode($result->output(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('<error>Error: Failed to parse script output.</error>');

            return;
        }

        $systemValues = $data['system'];

        $optimalValues = $data['optimal'];

        // Display the results
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
