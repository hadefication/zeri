<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Bootstrap;

use Dotenv\Dotenv;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use LaravelZero\Framework\Providers\Build\Build;




final class BuildLoadEnvironmentVariables implements BootstrapperContract
{



private $build;




public function __construct(Build $build)
{
$this->build = $build;
}




public function bootstrap(Application $app): void
{



if ($this->build->shouldUseEnvironmentFile()) {
Dotenv::createMutable($this->build->getDirectoryPath(), $this->build->environmentFile())->load();
}
}
}
