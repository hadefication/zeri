<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class DataProvider extends Metadata
{



private string $className;




private string $methodName;






protected function __construct(int $level, string $className, string $methodName)
{
parent::__construct($level);

$this->className = $className;
$this->methodName = $methodName;
}

public function isDataProvider(): true
{
return true;
}




public function className(): string
{
return $this->className;
}




public function methodName(): string
{
return $this->methodName;
}
}
