<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output;

/**
@no-named-arguments


*/
final readonly class NullPrinter implements Printer
{
public function print(string $buffer): void
{
}

public function flush(): void
{
}
}
