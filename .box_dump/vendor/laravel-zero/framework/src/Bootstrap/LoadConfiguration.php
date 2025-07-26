<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Bootstrap;

use Illuminate\Console\Application as Artisan;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;




final class LoadConfiguration implements BootstrapperContract
{



public function bootstrap(Application $app): void
{
$app->make(BaseLoadConfiguration::class)
->bootstrap($app);




Artisan::starting(
function ($artisan) use ($app) {
$artisan->setName($app['config']->get('app.name', 'Laravel Zero'));
$artisan->setVersion($app->version());
}
);
}
}
