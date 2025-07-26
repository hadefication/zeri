<?php

declare(strict_types=1);

namespace Pest\Plugins;

use Pest\Contracts\Plugins\HandlesArguments;




final class Environment implements HandlesArguments
{



public const CI = 'ci';




public const LOCAL = 'local';




private static ?string $name = null;




public function handleArguments(array $arguments): array
{
foreach ($arguments as $index => $argument) {
if ($argument === '--ci') {
unset($arguments[$index]);

self::$name = self::CI;
}
}

return array_values($arguments);
}




public static function name(?string $name = null): string
{
if (is_string($name)) {
self::$name = $name;
}

return self::$name ?? self::LOCAL;
}
}
