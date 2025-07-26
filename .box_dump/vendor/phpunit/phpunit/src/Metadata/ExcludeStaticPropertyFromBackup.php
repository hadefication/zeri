<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class ExcludeStaticPropertyFromBackup extends Metadata
{



private string $className;




private string $propertyName;






protected function __construct(int $level, string $className, string $propertyName)
{
parent::__construct($level);

$this->className = $className;
$this->propertyName = $propertyName;
}

public function isExcludeStaticPropertyFromBackup(): true
{
return true;
}




public function className(): string
{
return $this->className;
}




public function propertyName(): string
{
return $this->propertyName;
}
}
