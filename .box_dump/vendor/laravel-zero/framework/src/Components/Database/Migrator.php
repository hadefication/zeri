<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Database;

use Illuminate\Database\Migrations\Migrator as BaseMigrator;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

use function collect;






class Migrator extends BaseMigrator
{






public function getMigrationFiles($paths): array
{
return collect($paths)
->flatMap(
function ($path) {
if (Str::endsWith($path, '.php')) {
$finder = (new Finder)->in([dirname($path)])
->files()
->name(basename($path));
} else {
try {
$finder = (new Finder)->in([$path])
->files();
} catch (DirectoryNotFoundException $e) {
return [];
}
}

return collect($finder)
->map(
fn (SplFileInfo $file) => $file->getPathname()
)
->all();
}
)
->filter()
->sortBy(
fn ($file) => $this->getMigrationName($file)
)
->values()
->keyBy(
function ($file) {
return $this->getMigrationName($file);
}
)
->all();
}
}
