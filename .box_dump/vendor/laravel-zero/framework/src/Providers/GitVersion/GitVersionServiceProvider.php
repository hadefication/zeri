<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\GitVersion;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Process\Process;






final class GitVersionServiceProvider extends ServiceProvider
{



public function register(): void
{
$this->app->bind(
'git.version',
function (Application $app) {
$process = Process::fromShellCommandline(
'git describe --tags --abbrev=0',
$app->basePath()
);

$process->run();

return trim($process->getOutput()) ?: 'unreleased';
}
);
}
}
