<?php

declare(strict_types=1);

namespace Pest\Arch\Support;




final class UserDefinedFunctions
{





private static ?array $functions = null;






public static function get(): array
{
if (self::$functions === null) {
self::$functions = get_defined_functions()['user'];
}

return self::$functions;
}
}
