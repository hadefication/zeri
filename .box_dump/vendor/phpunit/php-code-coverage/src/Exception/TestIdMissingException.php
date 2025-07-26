<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage;

use RuntimeException;

final class TestIdMissingException extends RuntimeException implements Exception
{
public function __construct()
{
parent::__construct('Test ID is missing');
}
}
