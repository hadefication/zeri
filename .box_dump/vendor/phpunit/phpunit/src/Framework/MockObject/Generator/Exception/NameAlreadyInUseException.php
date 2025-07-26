<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
@no-named-arguments


*/
final class NameAlreadyInUseException extends \PHPUnit\Framework\Exception implements Exception
{



public function __construct(string $name)
{
parent::__construct(
sprintf(
'The name "%s" is already in use',
$name,
),
);
}
}
