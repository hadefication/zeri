<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function array_diff_assoc;
use function array_unique;
use function implode;
use function sprintf;

/**
@no-named-arguments


*/
final class DuplicateMethodException extends \PHPUnit\Framework\Exception implements Exception
{



public function __construct(array $methods)
{
parent::__construct(
sprintf(
'Cannot double using a method list that contains duplicates: "%s" (duplicate: "%s")',
implode(', ', $methods),
implode(', ', array_unique(array_diff_assoc($methods, array_unique($methods)))),
),
);
}
}
