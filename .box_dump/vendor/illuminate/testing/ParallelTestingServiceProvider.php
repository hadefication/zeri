<?php

namespace Illuminate\Testing;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Concerns\TestDatabases;

class ParallelTestingServiceProvider extends ServiceProvider implements DeferrableProvider
{
use TestDatabases;






public function boot()
{
if ($this->app->runningInConsole()) {
$this->bootTestDatabase();
}
}






public function register()
{
if ($this->app->runningInConsole()) {
$this->app->singleton(ParallelTesting::class, function () {
return new ParallelTesting($this->app);
});
}
}
}
