<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class PostCondition extends Metadata
{
private int $priority;




protected function __construct(int $level, int $priority)
{
parent::__construct($level);

$this->priority = $priority;
}

public function isPostCondition(): true
{
return true;
}

public function priority(): int
{
return $this->priority;
}
}
