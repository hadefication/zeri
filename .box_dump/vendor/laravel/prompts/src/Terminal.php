<?php

namespace Laravel\Prompts;

use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Terminal as SymfonyTerminal;

class Terminal
{



protected ?string $initialTtyMode;




protected SymfonyTerminal $terminal;




public function __construct()
{
$this->terminal = new SymfonyTerminal;
}




public function read(): string
{
$input = fread(STDIN, 1024);

return $input !== false ? $input : '';
}




public function setTty(string $mode): void
{
$this->initialTtyMode ??= $this->exec('stty -g');

$this->exec("stty $mode");
}




public function restoreTty(): void
{
if (isset($this->initialTtyMode)) {
$this->exec("stty {$this->initialTtyMode}");

$this->initialTtyMode = null;
}
}




public function cols(): int
{
return $this->terminal->getWidth();
}




public function lines(): int
{
return $this->terminal->getHeight();
}




public function initDimensions(): void
{
(new ReflectionClass($this->terminal))
->getMethod('initDimensions')
->invoke($this->terminal);
}




public function exit(): void
{
exit(1);
}




protected function exec(string $command): string
{
$process = proc_open($command, [
1 => ['pipe', 'w'],
2 => ['pipe', 'w'],
], $pipes);

if (! $process) {
throw new RuntimeException('Failed to create process.');
}

$stdout = stream_get_contents($pipes[1]);
$stderr = stream_get_contents($pipes[2]);
$code = proc_close($process);

if ($code !== 0 || $stdout === false) {
throw new RuntimeException(trim($stderr ?: "Unknown error (code: $code)"), $code);
}

return $stdout;
}
}
