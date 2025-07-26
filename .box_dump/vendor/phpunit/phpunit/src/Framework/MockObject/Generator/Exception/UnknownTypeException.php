<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
@no-named-arguments


*/
final class UnknownTypeException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $type)
{
parent::__construct(
sprintf(
'Class or interface "%s" does not exist',
$type,
),
);
}
}
