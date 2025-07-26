<?php declare(strict_types=1);








namespace PHPUnit\Util;

use function trim;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\PhptAssertionFailedError;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Runner\ErrorException;
use Throwable;

/**
@no-named-arguments


*/
final readonly class ThrowableToStringMapper
{
public static function map(Throwable $t): string
{
if ($t instanceof ErrorException) {
return $t->getMessage();
}

if ($t instanceof SelfDescribing) {
$buffer = $t->toString();

if ($t instanceof ExpectationFailedException && $t->getComparisonFailure()) {
$buffer .= $t->getComparisonFailure()->getDiff();
}

if ($t instanceof PhptAssertionFailedError) {
$buffer .= $t->diff();
}

if (!empty($buffer)) {
$buffer = trim($buffer) . "\n";
}

return $buffer;
}

return $t::class . ': ' . $t->getMessage() . "\n";
}
}
