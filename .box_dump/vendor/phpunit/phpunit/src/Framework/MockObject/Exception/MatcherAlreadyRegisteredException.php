<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;

/**
@no-named-arguments


*/
final class MatcherAlreadyRegisteredException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $id)
{
parent::__construct(
sprintf(
'Matcher with id <%s> is already registered',
$id,
),
);
}
}
