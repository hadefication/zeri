<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Driver;

use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception;

final class XdebugNotAvailableException extends RuntimeException implements Exception
{
public function __construct()
{
parent::__construct('The Xdebug extension is not available');
}
}
