<?php










namespace Symfony\Component\Console\Output;







interface ConsoleOutputInterface extends OutputInterface
{



public function getErrorOutput(): OutputInterface;

public function setErrorOutput(OutputInterface $error): void;

public function section(): ConsoleSectionOutput;
}
