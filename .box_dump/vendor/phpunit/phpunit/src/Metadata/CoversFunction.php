<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class CoversFunction extends Metadata
{



private string $functionName;





protected function __construct(int $level, string $functionName)
{
parent::__construct($level);

$this->functionName = $functionName;
}

public function isCoversFunction(): true
{
return true;
}




public function functionName(): string
{
return $this->functionName;
}




public function asStringForCodeUnitMapper(): string
{
return '::' . $this->functionName;
}
}
