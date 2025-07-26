<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class UsesTrait extends Metadata
{



private string $traitName;





protected function __construct(int $level, string $traitName)
{
parent::__construct($level);

$this->traitName = $traitName;
}

public function isUsesTrait(): true
{
return true;
}




public function traitName(): string
{
return $this->traitName;
}






public function asStringForCodeUnitMapper(): string
{
return $this->traitName;
}
}
