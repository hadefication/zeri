<?php declare(strict_types=1);








namespace PHPUnit\TextUI;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class TestDirectoryNotFoundException extends RuntimeException implements Exception
{
public function __construct(string $path)
{
parent::__construct(
sprintf(
'Test directory "%s" not found',
$path,
),
);
}
}
