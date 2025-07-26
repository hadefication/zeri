<?php declare(strict_types=1);








namespace PHPUnit\Runner\Baseline;

/**
@no-named-arguments


*/
abstract readonly class Subscriber
{
private Generator $generator;

public function __construct(Generator $generator)
{
$this->generator = $generator;
}

protected function generator(): Generator
{
return $this->generator;
}
}
