<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class PreserveGlobalState extends Metadata
{
private bool $enabled;




protected function __construct(int $level, bool $enabled)
{
parent::__construct($level);

$this->enabled = $enabled;
}

public function isPreserveGlobalState(): true
{
return true;
}

public function enabled(): bool
{
return $this->enabled;
}
}
