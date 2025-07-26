<?php

































declare(strict_types=1);










namespace PHPUnit\Event\Code;

use NunoMaduro\Collision\Contracts\RenderableOnCollisionEditor;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Exception;
use PHPUnit\Util\Filter;
use PHPUnit\Util\ThrowableToStringMapper;




final readonly class ThrowableBuilder
{




public static function from(\Throwable $t): Throwable
{
$previous = $t->getPrevious();

if ($previous !== null) {
$previous = self::from($previous);
}

$trace = Filter::stackTraceFromThrowableAsString($t);

if ($t instanceof RenderableOnCollisionEditor && $frame = $t->toCollisionEditor()) {
$file = $frame->getFile();
$line = $frame->getLine();

$trace = "$file:$line\n$trace";
}

return new Throwable(
$t::class,
$t->getMessage(),
ThrowableToStringMapper::map($t),
$trace,
$previous
);
}
}
