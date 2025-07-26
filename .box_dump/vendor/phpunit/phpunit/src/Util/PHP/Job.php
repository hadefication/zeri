<?php declare(strict_types=1);








namespace PHPUnit\Util\PHP;

/**
@immutable
@no-named-arguments



*/
final readonly class Job
{



private string $code;




private array $phpSettings;




private array $environmentVariables;




private array $arguments;




private ?string $input;
private bool $redirectErrors;








public function __construct(string $code, array $phpSettings = [], array $environmentVariables = [], array $arguments = [], ?string $input = null, bool $redirectErrors = false)
{
$this->code = $code;
$this->phpSettings = $phpSettings;
$this->environmentVariables = $environmentVariables;
$this->arguments = $arguments;
$this->input = $input;
$this->redirectErrors = $redirectErrors;
}




public function code(): string
{
return $this->code;
}




public function phpSettings(): array
{
return $this->phpSettings;
}

/**
@phpstan-assert-if-true
*/
public function hasEnvironmentVariables(): bool
{
return $this->environmentVariables !== [];
}




public function environmentVariables(): array
{
return $this->environmentVariables;
}

/**
@phpstan-assert-if-true
*/
public function hasArguments(): bool
{
return $this->arguments !== [];
}




public function arguments(): array
{
return $this->arguments;
}

/**
@phpstan-assert-if-true
*/
public function hasInput(): bool
{
return $this->input !== null;
}






public function input(): string
{
if ($this->input === null) {
throw new PhpProcessException('No input specified');
}

return $this->input;
}

public function redirectErrors(): bool
{
return $this->redirectErrors;
}
}
