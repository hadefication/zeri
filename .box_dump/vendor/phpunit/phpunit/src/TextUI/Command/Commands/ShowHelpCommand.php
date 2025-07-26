<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use PHPUnit\TextUI\Help;

/**
@no-named-arguments


*/
final readonly class ShowHelpCommand implements Command
{
private int $shellExitCode;

public function __construct(int $shellExitCode)
{
$this->shellExitCode = $shellExitCode;
}

public function execute(): Result
{
return Result::from(
(new Help)->generate(),
$this->shellExitCode,
);
}
}
