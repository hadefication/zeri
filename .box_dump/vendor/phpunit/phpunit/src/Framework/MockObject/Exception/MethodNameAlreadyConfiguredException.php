<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

/**
@no-named-arguments


*/
final class MethodNameAlreadyConfiguredException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct()
{
parent::__construct('Method name is already configured');
}
}
