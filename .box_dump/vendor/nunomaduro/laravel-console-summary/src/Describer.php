<?php

declare(strict_types=1);










namespace NunoMaduro\LaravelConsoleSummary;

use Illuminate\Console\Application;
use Illuminate\Contracts\Config\Repository;
use NunoMaduro\LaravelConsoleSummary\Contracts\DescriberContract;
use Symfony\Component\Console\Output\OutputInterface;

class Describer implements DescriberContract
{



private int $width = 0;

public function __construct(private readonly Repository $config) {}




public function describe(Application $application, OutputInterface $output): void
{
$this->describeTitle($application, $output)
->describeUsage($output)
->describeCommands($application, $output);
}




protected function describeTitle(Application $application, OutputInterface $output): DescriberContract
{
$output->write(
"\n  <fg=white;options=bold>{$application->getName()} </> <fg=green;options=bold>{$application->getVersion()}</>\n\n"
);

return $this;
}




protected function describeUsage(OutputInterface $output): DescriberContract
{
$binary = $this->config->get('laravel-console-summary.binary', ARTISAN_BINARY);
$output->write("  <fg=yellow;options=bold>USAGE:</> {$binary} <command> [options] [arguments]\n");

return $this;
}




protected function describeCommands(Application $application, OutputInterface $output): DescriberContract
{
$this->width = 0;

$hide = collect($this->config->get('laravel-console-summary.hide', []));

collect($application->all())->filter(fn ($command) => ! $command->isHidden())->filter(function ($command) use ($hide) {
$nameParts = explode(':', $name = $command->getName());

$hasExactMatch = $hide->contains($command->getName());
$hasWildcardMatch = $hide->contains($nameParts[0].':*');

return ! $hasExactMatch && ! $hasWildcardMatch;
})->unique(fn ($command) => $command->getName())->groupBy(function ($command) {
$nameParts = explode(':', $name = $command->getName());
$this->width = max($this->width, mb_strlen($name));

return isset($nameParts[1]) ? $nameParts[0] : '';
})->sortKeys()->each(function ($commands) use ($output) {
$output->write("\n");

$commands = $commands->toArray();

usort($commands, function ($a, $b) {
return strcmp($a->getName(), $b->getName());
});

foreach ($commands as $command) {
$output->write(sprintf(
"  <fg=green>%s</>%s%s%s\n",
$command->getName(),
str_repeat(' ', $this->width - mb_strlen($command->getName()) + 1),
$command->getAliases() ? '<fg=cyan>[</>'.implode('|', $command->getAliases()).'<fg=cyan>]</> ' : '',
$command->getDescription()
));
}
})->whenNotEmpty(function () use ($output) {
$output->writeln('');
});

return $this;
}
}
