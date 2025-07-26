<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

use const PHP_EOL;
use function sprintf;
use PHPUnit\Exception;
use RuntimeException;

/**
@no-named-arguments


*/
final class InvalidAttributeException extends RuntimeException implements Exception
{







public function __construct(string $attributeName, string $target, string $file, int $line, string $message)
{
parent::__construct(
sprintf(
'Invalid attribute %s for %s in %s:%d%s%s',
$attributeName,
$target,
$file,
$line,
PHP_EOL,
$message,
),
);
}
}
