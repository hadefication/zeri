<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Driver;

use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception;

final class PcovNotAvailableException extends RuntimeException implements Exception
{
public function __construct()
{
parent::__construct('The PCOV extension is not available');
}
}
