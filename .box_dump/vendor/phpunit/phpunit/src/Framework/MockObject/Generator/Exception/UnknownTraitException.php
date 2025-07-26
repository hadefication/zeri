<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
@no-named-arguments




*/
final class UnknownTraitException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $traitName)
{
parent::__construct(
sprintf(
'Trait "%s" does not exist',
$traitName,
),
);
}
}
