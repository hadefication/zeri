<?php declare(strict_types=1);








namespace PHPUnit\Util\PHP;

/**
@immutable
@no-named-arguments



*/
final readonly class Result
{
private string $stdout;
private string $stderr;

public function __construct(string $stdout, string $stderr)
{
$this->stdout = $stdout;
$this->stderr = $stderr;
}

public function stdout(): string
{
return $this->stdout;
}

public function stderr(): string
{
return $this->stderr;
}
}
