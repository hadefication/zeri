<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Bootstrap;

use Dotenv\Dotenv;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables as BaseLoadEnvironmentVariables;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;

use function class_exists;




final class LoadEnvironmentVariables implements BootstrapperContract
{



public function bootstrap(Application $app): void
{
if (class_exists(Dotenv::class)) {
if (file_exists($app->environmentFilePath())) {
$app->make(BaseLoadEnvironmentVariables::class)->bootstrap($app);
}

$app->make(BuildLoadEnvironmentVariables::class)->bootstrap($app);
}
}
}
