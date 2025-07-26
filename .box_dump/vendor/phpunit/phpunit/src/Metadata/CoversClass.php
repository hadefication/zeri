<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class CoversClass extends Metadata
{



private string $className;





protected function __construct(int $level, string $className)
{
parent::__construct($level);

$this->className = $className;
}

public function isCoversClass(): true
{
return true;
}




public function className(): string
{
return $this->className;
}






public function asStringForCodeUnitMapper(): string
{
return $this->className;
}
}
