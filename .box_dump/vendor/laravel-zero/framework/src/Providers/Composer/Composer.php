<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\Composer;

use Illuminate\Contracts\Foundation\Application;
use LaravelZero\Framework\Contracts\Providers\ComposerContract;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Throwable;

use function collect;






final class Composer implements ComposerContract
{





public function __construct(private readonly Application $app) {}




public function require(string $package, bool $dev = false): bool
{
return $this->run(
"composer require $package".($dev ? ' --dev' : ''),
$this->app->basePath()
);
}


public function remove(string $package, bool $dev = false): bool
{
return $this->run(
"composer remove $package".($dev ? ' --dev' : ''),
$this->app->basePath()
);
}




public function createProject(string $skeleton, string $projectName, array $options): bool
{
$cmd = collect(["composer create-project {$skeleton} {$projectName}"])
->merge($options)
->implode(' ');

return $this->run($cmd);
}




private function run(string $cmd, ?string $cwd = null): bool
{
$process = Process::fromShellCommandline($cmd, $cwd);

$process->setTimeout(900);

if ($process->isTty()) {
$process->setTty(true);
}

try {
$output = $this->app->make(OutputInterface::class);
} catch (Throwable $e) {
$output = null;
}

if ($output) {
$output->write("\n");
$process->run(
function ($type, $line) use ($output) {
$output->write($line);
}
);
} else {
$process->run();
}

return $process->isSuccessful();
}
}
