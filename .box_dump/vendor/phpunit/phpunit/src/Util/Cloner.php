<?php declare(strict_types=1);








namespace PHPUnit\Util;

use Throwable;

/**
@no-named-arguments


*/
final readonly class Cloner
{
/**
@template




*/
public static function clone(object $original): object
{
try {
return clone $original;

/**
@phpstan-ignore */
} catch (Throwable) {
return $original;
}
}
}
