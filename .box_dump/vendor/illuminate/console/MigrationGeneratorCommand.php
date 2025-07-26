<?php

namespace Illuminate\Console;

use Illuminate\Filesystem\Filesystem;

use function Illuminate\Filesystem\join_paths;

abstract class MigrationGeneratorCommand extends Command
{





protected $files;






public function __construct(Filesystem $files)
{
parent::__construct();

$this->files = $files;
}






abstract protected function migrationTableName();






abstract protected function migrationStubFile();






public function handle()
{
$table = $this->migrationTableName();

if ($this->migrationExists($table)) {
$this->components->error('Migration already exists.');

return 1;
}

$this->replaceMigrationPlaceholders(
$this->createBaseMigration($table), $table
);

$this->components->info('Migration created successfully.');

return 0;
}







protected function createBaseMigration($table)
{
return $this->laravel['migration.creator']->create(
'create_'.$table.'_table', $this->laravel->databasePath('/migrations')
);
}








protected function replaceMigrationPlaceholders($path, $table)
{
$stub = str_replace(
'{{table}}', $table, $this->files->get($this->migrationStubFile())
);

$this->files->put($path, $stub);
}







protected function migrationExists($table)
{
return count($this->files->glob(
join_paths($this->laravel->databasePath('migrations'), '*_*_*_*_create_'.$table.'_table.php')
)) !== 0;
}
}
