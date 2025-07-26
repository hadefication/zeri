<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

/**
@no-named-arguments


*/
abstract readonly class Subscriber
{
private Collector $collector;

public function __construct(Collector $collector)
{
$this->collector = $collector;
}

protected function collector(): Collector
{
return $this->collector;
}
}
