<?php

namespace Illuminate\Cache\Console;

use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'cache:forget')]
class ForgetCommand extends Command
{





protected $signature = 'cache:forget {key : The key to remove} {store? : The store to remove the key from}';






protected $description = 'Remove an item from the cache';






protected $cache;






public function __construct(CacheManager $cache)
{
parent::__construct();

$this->cache = $cache;
}






public function handle()
{
$this->cache->store($this->argument('store'))->forget(
$this->argument('key')
);

$this->components->info('The ['.$this->argument('key').'] key has been removed from the cache.');
}
}
