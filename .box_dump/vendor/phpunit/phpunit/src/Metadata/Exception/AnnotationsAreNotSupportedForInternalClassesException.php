<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

use function sprintf;
use PHPUnit\Exception;
use RuntimeException;

/**
@no-named-arguments


*/
final class AnnotationsAreNotSupportedForInternalClassesException extends RuntimeException implements Exception
{



public function __construct(string $className)
{
parent::__construct(
sprintf(
'Annotations can only be parsed for user-defined classes, trying to parse annotations for class "%s"',
$className,
),
);
}
}
