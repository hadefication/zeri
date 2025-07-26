<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Driver;

use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception;

final class XdebugNotEnabledException extends RuntimeException implements Exception
{
public function __construct()
{
parent::__construct('XDEBUG_MODE=coverage (environment variable) or xdebug.mode=coverage (PHP configuration setting) has to be set');
}
}
