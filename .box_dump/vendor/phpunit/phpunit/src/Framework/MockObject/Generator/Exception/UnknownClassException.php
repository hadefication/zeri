<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
@no-named-arguments


*/
final class UnknownClassException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $className)
{
parent::__construct(
sprintf(
'Class "%s" does not exist',
$className,
),
);
}
}
