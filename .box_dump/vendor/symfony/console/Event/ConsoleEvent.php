<?php










namespace Symfony\Component\Console\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\Event;






class ConsoleEvent extends Event
{
public function __construct(
protected ?Command $command,
private InputInterface $input,
private OutputInterface $output,
) {
}




public function getCommand(): ?Command
{
return $this->command;
}




public function getInput(): InputInterface
{
return $this->input;
}




public function getOutput(): OutputInterface
{
return $this->output;
}
}
