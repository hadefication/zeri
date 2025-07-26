<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Contracts;

use LaravelZero\Framework\Application;




interface BootstrapperContract
{




public function bootstrap(Application $app): void;
}
