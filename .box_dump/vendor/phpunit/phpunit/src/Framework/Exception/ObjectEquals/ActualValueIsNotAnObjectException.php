<?php declare(strict_types=1);








namespace PHPUnit\Framework;

/**
@no-named-arguments


*/
final class ActualValueIsNotAnObjectException extends Exception
{
public function __construct()
{
parent::__construct(
'Actual value is not an object',
);
}
}
