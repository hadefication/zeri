<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;

/**
@no-named-arguments


*/
final class MatchBuilderNotFoundException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $id)
{
parent::__construct(
sprintf(
'No builder found for match builder identification <%s>',
$id,
),
);
}
}
