<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class ExcludeGlobalVariableFromBackup extends Metadata
{



private string $globalVariableName;





protected function __construct(int $level, string $globalVariableName)
{
parent::__construct($level);

$this->globalVariableName = $globalVariableName;
}

public function isExcludeGlobalVariableFromBackup(): true
{
return true;
}




public function globalVariableName(): string
{
return $this->globalVariableName;
}
}
