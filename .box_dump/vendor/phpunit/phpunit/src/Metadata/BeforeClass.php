<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class BeforeClass extends Metadata
{
private int $priority;




protected function __construct(int $level, int $priority)
{
parent::__construct($level);

$this->priority = $priority;
}

public function isBeforeClass(): true
{
return true;
}

public function priority(): int
{
return $this->priority;
}
}
