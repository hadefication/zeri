<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class UnsupportedPhptSectionException extends RuntimeException implements Exception
{
public function __construct(string $section)
{
parent::__construct(
sprintf(
'PHPUnit does not support PHPT %s sections',
$section,
),
);
}
}
