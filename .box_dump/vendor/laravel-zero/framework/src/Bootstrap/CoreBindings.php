<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Bootstrap;

use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use LaravelZero\Framework\Providers\GitVersion\GitVersionServiceProvider;




final class CoreBindings implements BootstrapperContract
{



public function bootstrap(Application $app): void
{
(new GitVersionServiceProvider($app))->register();
}
}
