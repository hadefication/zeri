<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use function sprintf;

/**
@no-named-arguments


*/
final class UnknownClassOrInterfaceException extends InvalidArgumentException
{
public function __construct(string $name)
{
parent::__construct(
sprintf(
'Class or interface "%s" does not exist',
$name,
),
);
}
}
