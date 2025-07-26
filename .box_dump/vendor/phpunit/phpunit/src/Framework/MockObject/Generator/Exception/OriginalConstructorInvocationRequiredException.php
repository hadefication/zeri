<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

/**
@no-named-arguments


*/
final class OriginalConstructorInvocationRequiredException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct()
{
parent::__construct('Proxying to original methods requires invoking the original constructor');
}
}
