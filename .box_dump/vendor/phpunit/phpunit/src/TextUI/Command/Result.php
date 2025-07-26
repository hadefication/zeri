<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

/**
@immutable
@no-named-arguments



*/
final readonly class Result
{
public const SUCCESS = 0;
public const FAILURE = 1;
public const EXCEPTION = 2;
public const CRASH = 255;
private string $output;
private int $shellExitCode;

public static function from(string $output = '', int $shellExitCode = self::SUCCESS): self
{
return new self($output, $shellExitCode);
}

private function __construct(string $output, int $shellExitCode)
{
$this->output = $output;
$this->shellExitCode = $shellExitCode;
}

public function output(): string
{
return $this->output;
}

public function shellExitCode(): int
{
return $this->shellExitCode;
}
}
