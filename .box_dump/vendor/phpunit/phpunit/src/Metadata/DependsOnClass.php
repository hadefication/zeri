<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class DependsOnClass extends Metadata
{



private string $className;
private bool $deepClone;
private bool $shallowClone;





protected function __construct(int $level, string $className, bool $deepClone, bool $shallowClone)
{
parent::__construct($level);

$this->className = $className;
$this->deepClone = $deepClone;
$this->shallowClone = $shallowClone;
}

public function isDependsOnClass(): true
{
return true;
}




public function className(): string
{
return $this->className;
}

public function deepClone(): bool
{
return $this->deepClone;
}

public function shallowClone(): bool
{
return $this->shallowClone;
}
}
