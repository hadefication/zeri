<?php declare(strict_types=1);








namespace PHPUnit\Framework;

/**
@immutable
@no-named-arguments



*/
final class PhptAssertionFailedError extends AssertionFailedError
{
private readonly string $syntheticFile;
private readonly int $syntheticLine;




private readonly array $syntheticTrace;
private readonly string $diff;




public function __construct(string $message, int $code, string $file, int $line, array $trace, string $diff)
{
parent::__construct($message, $code);

$this->syntheticFile = $file;
$this->syntheticLine = $line;
$this->syntheticTrace = $trace;
$this->diff = $diff;
}

public function syntheticFile(): string
{
return $this->syntheticFile;
}

public function syntheticLine(): int
{
return $this->syntheticLine;
}




public function syntheticTrace(): array
{
return $this->syntheticTrace;
}

public function diff(): string
{
return $this->diff;
}
}
