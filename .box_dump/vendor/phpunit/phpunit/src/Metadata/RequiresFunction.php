<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class RequiresFunction extends Metadata
{



private string $functionName;





protected function __construct(int $level, string $functionName)
{
parent::__construct($level);

$this->functionName = $functionName;
}

public function isRequiresFunction(): true
{
return true;
}




public function functionName(): string
{
return $this->functionName;
}
}
