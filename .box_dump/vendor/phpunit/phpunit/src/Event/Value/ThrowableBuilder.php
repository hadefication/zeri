<?php declare(strict_types=1);








namespace PHPUnit\Event\Code;

use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Exception;
use PHPUnit\Util\Filter;
use PHPUnit\Util\ThrowableToStringMapper;

/**
@no-named-arguments


*/
final readonly class ThrowableBuilder
{




public static function from(\Throwable $t): Throwable
{
$previous = $t->getPrevious();

if ($previous !== null) {
$previous = self::from($previous);
}

return new Throwable(
$t::class,
$t->getMessage(),
ThrowableToStringMapper::map($t),
Filter::stackTraceFromThrowableAsString($t, false),
$previous,
);
}
}
