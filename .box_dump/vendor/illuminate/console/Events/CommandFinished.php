<?php

namespace Illuminate\Console\Events;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandFinished
{








public function __construct(
public string $command,
public InputInterface $input,
public OutputInterface $output,
public int $exitCode,
) {
}
}
