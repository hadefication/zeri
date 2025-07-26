<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage;

use RuntimeException;

final class ReportAlreadyFinalizedException extends RuntimeException implements Exception
{
public function __construct()
{
parent::__construct('The code coverage report has already been finalized');
}
}
