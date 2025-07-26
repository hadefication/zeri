<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Provider;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use NunoMaduro\Collision\Writer;
use Spatie\Ignition\Contracts\SolutionProviderRepository;






class CollisionServiceProvider extends ServiceProvider
{



protected bool $defer = true;




public function boot(): void
{
$this->commands([
TestCommand::class,
]);
}




public function register(): void
{
if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
$this->app->bind(Provider::class, function () {
if ($this->app->has(SolutionProviderRepository::class)) { 

$solutionProviderRepository = $this->app->get(SolutionProviderRepository::class); 

$solutionsRepository = new IgnitionSolutionsRepository($solutionProviderRepository);
} else {
$solutionsRepository = new NullSolutionsRepository;
}

$writer = new Writer($solutionsRepository);
$handler = new Handler($writer);

return new Provider(null, $handler);
});


$appExceptionHandler = $this->app->make(ExceptionHandlerContract::class);

$this->app->singleton(
ExceptionHandlerContract::class,
function ($app) use ($appExceptionHandler) {
return new ExceptionHandler($app, $appExceptionHandler);
}
);
}
}




public function provides()
{
return [Provider::class];
}
}
