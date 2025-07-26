<?php

declare(strict_types=1);

namespace Pest\Plugins;

use Pest\Contracts\Plugins\Terminable;
use Pest\PendingCalls\TestCall;




final class Only implements Terminable
{



private const TEMPORARY_FOLDER = __DIR__
.DIRECTORY_SEPARATOR
.'..'
.DIRECTORY_SEPARATOR
.'..'
.DIRECTORY_SEPARATOR
.'.temp';




public function terminate(): void
{
if (Parallel::isWorker()) {
return;
}

$lockFile = self::TEMPORARY_FOLDER.DIRECTORY_SEPARATOR.'only.lock';

if (file_exists($lockFile)) {
unlink($lockFile);
}
}




public static function enable(TestCall $testCall, string $group = '__pest_only'): void
{
$testCall->group($group);

if (Environment::name() === Environment::CI || Parallel::isWorker()) {
return;
}

$lockFile = self::TEMPORARY_FOLDER.DIRECTORY_SEPARATOR.'only.lock';

if (file_exists($lockFile) && $group === '__pest_only') {
file_put_contents($lockFile, $group);

return;
}

if (! file_exists($lockFile)) {
touch($lockFile);

file_put_contents($lockFile, $group);
}
}




public static function isEnabled(): bool
{
$lockFile = self::TEMPORARY_FOLDER.DIRECTORY_SEPARATOR.'only.lock';

return file_exists($lockFile);
}




public static function group(): string
{
$lockFile = self::TEMPORARY_FOLDER.DIRECTORY_SEPARATOR.'only.lock';

if (! file_exists($lockFile)) {
return '__pest_only';
}

return file_get_contents($lockFile) ?: '__pest_only'; 
}
}
