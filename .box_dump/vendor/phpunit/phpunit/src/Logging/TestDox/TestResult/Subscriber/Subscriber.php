<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

/**
@no-named-arguments


*/
abstract readonly class Subscriber
{
private TestResultCollector $collector;

public function __construct(TestResultCollector $collector)
{
$this->collector = $collector;
}

protected function collector(): TestResultCollector
{
return $this->collector;
}
}
