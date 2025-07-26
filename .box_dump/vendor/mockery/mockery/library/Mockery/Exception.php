<?php









namespace Mockery;

use Mockery\Exception\MockeryExceptionInterface;
use UnexpectedValueException;

class Exception extends UnexpectedValueException implements MockeryExceptionInterface
{
}
