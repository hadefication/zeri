<?php declare(strict_types=1);








namespace PHPUnit\Runner\Baseline;

use function sprintf;
use PHPUnit\Runner\Exception;
use RuntimeException;

/**
@no-named-arguments


*/
final class FileDoesNotHaveLineException extends RuntimeException implements Exception
{
public function __construct(string $file, int $line)
{
parent::__construct(
sprintf(
'File "%s" does not have line %d',
$file,
$line,
),
);
}
}
