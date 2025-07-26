<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

/**
@no-named-arguments




*/
final class CannotCloneTestDoubleForReadonlyClassException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct()
{
parent::__construct(
'Cloning test doubles for readonly classes is not supported on PHP 8.2',
);
}
}
