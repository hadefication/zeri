<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use function sprintf;

/**
@no-named-arguments


*/
final class UnknownTypeException extends InvalidArgumentException
{
public function __construct(string $name)
{
parent::__construct(
sprintf(
'Type "%s" is not known',
$name,
),
);
}
}
