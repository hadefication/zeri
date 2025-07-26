<?php

namespace Illuminate\Cache\Console;

use Illuminate\Console\MigrationGeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:cache-table', aliases: ['cache:table'])]
class CacheTableCommand extends MigrationGeneratorCommand
{





protected $name = 'make:cache-table';






protected $aliases = ['cache:table'];






protected $description = 'Create a migration for the cache database table';






protected function migrationTableName()
{
return 'cache';
}






protected function migrationStubFile()
{
return __DIR__.'/stubs/cache.stub';
}
}
