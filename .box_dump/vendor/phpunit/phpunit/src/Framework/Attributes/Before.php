<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Before
{
private int $priority;

public function __construct(int $priority = 0)
{
$this->priority = $priority;
}

public function priority(): int
{
return $this->priority;
}
}
