<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

/**
@no-named-arguments


*/
abstract readonly class Subscriber
{
private ProgressPrinter $printer;

public function __construct(ProgressPrinter $printer)
{
$this->printer = $printer;
}

protected function printer(): ProgressPrinter
{
return $this->printer;
}
}
