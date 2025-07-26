<?php

namespace Illuminate\Foundation;

use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Foundation\MaintenanceMode;

class CacheBasedMaintenanceMode implements MaintenanceMode
{





protected $cache;






protected $store;






protected $key;








public function __construct(Factory $cache, string $store, string $key)
{
$this->cache = $cache;
$this->store = $store;
$this->key = $key;
}







public function activate(array $payload): void
{
$this->getStore()->put($this->key, $payload);
}






public function deactivate(): void
{
$this->getStore()->forget($this->key);
}






public function active(): bool
{
return $this->getStore()->has($this->key);
}






public function data(): array
{
return $this->getStore()->get($this->key);
}






protected function getStore(): Repository
{
return $this->cache->store($this->store);
}
}
