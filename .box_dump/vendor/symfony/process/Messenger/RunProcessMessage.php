<?php










namespace Symfony\Component\Process\Messenger;




class RunProcessMessage implements \Stringable
{
public ?string $commandLine = null;

public function __construct(
public readonly array $command,
public readonly ?string $cwd = null,
public readonly ?array $env = null,
public readonly mixed $input = null,
public readonly ?float $timeout = 60.0,
) {
}

public function __toString(): string
{
return $this->commandLine ?? implode(' ', $this->command);
}






public static function fromShellCommandline(string $command, ?string $cwd = null, ?array $env = null, mixed $input = null, ?float $timeout = 60): self
{
$message = new self([], $cwd, $env, $input, $timeout);
$message->commandLine = $command;

return $message;
}
}
