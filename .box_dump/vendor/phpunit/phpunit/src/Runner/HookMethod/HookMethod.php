<?php declare(strict_types=1);








namespace PHPUnit\Runner;

/**
@no-named-arguments


*/
final readonly class HookMethod
{



private string $methodName;
private int $priority;




public function __construct(string $methodName, int $priority)
{
$this->methodName = $methodName;
$this->priority = $priority;
}




public function methodName(): string
{
return $this->methodName;
}

public function priority(): int
{
return $this->priority;
}
}
