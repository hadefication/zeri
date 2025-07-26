<?php

declare(strict_types=1);











namespace PhpCsFixer\Runner\Parallel;























use Illuminate\Support\ProcessUtils;
use PhpCsFixer\Runner\RunnerConfig;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

/**
@readonly








*/
final class ProcessFactory
{
private InputInterface $input;

public function __construct(InputInterface $input)
{
$this->input = $input;
}

public function create(
LoopInterface $loop,
RunnerConfig $runnerConfig,
ProcessIdentifier $identifier,
int $serverPort
): Process {
$commandArgs = $this->getCommandArgs($serverPort, $identifier, $runnerConfig);

return new Process(
implode(' ', $commandArgs),
$loop,
$runnerConfig->getParallelConfig()->getProcessTimeout()
);
}






public function getCommandArgs(int $serverPort, ProcessIdentifier $identifier, RunnerConfig $runnerConfig): array
{
$phpBinary = (new PhpExecutableFinder)->find(false);

if ($phpBinary === false) {
throw new ParallelisationException('Cannot find PHP executable.');
}

$mainScript = $_SERVER['argv'][0];

$commandArgs = [
escapeshellarg($phpBinary),
escapeshellarg($mainScript),
'worker',
'--port',
(string) $serverPort,
'--identifier',
escapeshellarg($identifier->toString()),
];

if ($runnerConfig->isDryRun()) {
$commandArgs[] = '--dry-run';
}

if (filter_var($this->input->getOption('diff'), FILTER_VALIDATE_BOOLEAN)) {
$commandArgs[] = '--diff';
}

if (filter_var($this->input->getOption('stop-on-violation'), FILTER_VALIDATE_BOOLEAN)) {
$commandArgs[] = '--stop-on-violation';
}

foreach (['allow-risky', 'config', 'rules', 'using-cache', 'cache-file'] as $option) {
$optionValue = $this->input->getOption($option);

if ($optionValue !== null) {
$commandArgs[] = "--{$option}";
$commandArgs[] = ProcessUtils::escapeArgument($optionValue);
}
}

return $commandArgs;
}
}
