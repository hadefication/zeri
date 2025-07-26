<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\Cache;

use Illuminate\Cache\CacheServiceProvider as BaseServiceProvider;




final class CacheServiceProvider extends BaseServiceProvider
{



public function register(): void
{
parent::register();

if ($this->app['config']->get('cache') === null) {
$this->app['config']->set('cache', $this->getDefaultConfig());
}
}






private function getDefaultConfig(): array
{
return [
'default' => 'array',
'stores' => [
'array' => [
'driver' => 'array',
],
],
];
}
}
