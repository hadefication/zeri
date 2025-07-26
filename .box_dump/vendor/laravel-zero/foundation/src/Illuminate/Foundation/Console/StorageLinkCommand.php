<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'storage:link')]
class StorageLinkCommand extends Command
{





protected $signature = 'storage:link
                {--relative : Create the symbolic link using relative paths}
                {--force : Recreate existing symbolic links}';






protected $description = 'Create the symbolic links configured for the application';






public function handle()
{
$relative = $this->option('relative');

foreach ($this->links() as $link => $target) {
if (file_exists($link) && ! $this->isRemovableSymlink($link, $this->option('force'))) {
$this->components->error("The [$link] link already exists.");
continue;
}

if (is_link($link)) {
$this->laravel->make('files')->delete($link);
}

if ($relative) {
$this->laravel->make('files')->relativeLink($target, $link);
} else {
$this->laravel->make('files')->link($target, $link);
}

$this->components->info("The [$link] link has been connected to [$target].");
}
}






protected function links()
{
return $this->laravel['config']['filesystems.links'] ??
[public_path('storage') => storage_path('app/public')];
}








protected function isRemovableSymlink(string $link, bool $force): bool
{
return is_link($link) && $force;
}
}
