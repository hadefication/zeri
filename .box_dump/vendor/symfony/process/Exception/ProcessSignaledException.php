<?php










namespace Symfony\Component\Process\Exception;

use Symfony\Component\Process\Process;






final class ProcessSignaledException extends RuntimeException
{
public function __construct(
private Process $process,
) {
parent::__construct(\sprintf('The process has been signaled with signal "%s".', $process->getTermSignal()));
}

public function getProcess(): Process
{
return $this->process;
}

public function getSignal(): int
{
return $this->getProcess()->getTermSignal();
}
}
