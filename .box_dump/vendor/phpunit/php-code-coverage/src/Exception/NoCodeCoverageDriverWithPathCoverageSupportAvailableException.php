<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage;

use RuntimeException;

final class NoCodeCoverageDriverWithPathCoverageSupportAvailableException extends RuntimeException implements Exception
{
public function __construct()
{
parent::__construct('No code coverage driver with path coverage support available');
}
}
