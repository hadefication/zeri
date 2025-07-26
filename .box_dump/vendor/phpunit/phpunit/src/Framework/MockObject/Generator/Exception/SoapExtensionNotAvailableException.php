<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

/**
@no-named-arguments


*/
final class SoapExtensionNotAvailableException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct()
{
parent::__construct(
'The SOAP extension is required to generate a test double from WSDL',
);
}
}
