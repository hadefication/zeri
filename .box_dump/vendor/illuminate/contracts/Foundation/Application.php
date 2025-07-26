<?php

namespace Illuminate\Contracts\Foundation;

use Illuminate\Contracts\Container\Container;

interface Application extends Container
{





public function version();







public function basePath($path = '');







public function bootstrapPath($path = '');







public function configPath($path = '');







public function databasePath($path = '');







public function langPath($path = '');







public function publicPath($path = '');







public function resourcePath($path = '');







public function storagePath($path = '');







public function environment(...$environments);






public function runningInConsole();






public function runningUnitTests();






public function hasDebugModeEnabled();






public function maintenanceMode();






public function isDownForMaintenance();






public function registerConfiguredProviders();








public function register($provider, $force = false);








public function registerDeferredProvider($provider, $service = null);







public function resolveProvider($provider);






public function boot();







public function booting($callback);







public function booted($callback);







public function bootstrapWith(array $bootstrappers);






public function getLocale();








public function getNamespace();







public function getProviders($provider);






public function hasBeenBootstrapped();






public function loadDeferredProviders();







public function setLocale($locale);






public function shouldSkipMiddleware();







public function terminating($callback);






public function terminate();
}
