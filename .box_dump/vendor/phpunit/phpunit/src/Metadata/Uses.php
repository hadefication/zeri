<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class Uses extends Metadata
{



private string $target;





protected function __construct(int $level, string $target)
{
parent::__construct($level);

$this->target = $target;
}

public function isUses(): true
{
return true;
}




public function target(): string
{
return $this->target;
}
}
