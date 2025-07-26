<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class ClassIsAbstractException extends RuntimeException implements Exception
{
public function __construct(string $className, string $file)
{
parent::__construct(
sprintf(
'Class %s declared in %s is abstract',
$className,
$file,
),
);
}
}
