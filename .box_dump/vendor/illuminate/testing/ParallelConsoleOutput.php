<?php

namespace Illuminate\Testing;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

class ParallelConsoleOutput extends ConsoleOutput
{





protected $output;






protected $ignore = [
'Running phpunit in',
'Configuration read from',
];






public function __construct($output)
{
parent::__construct(
$output->getVerbosity(),
$output->isDecorated(),
$output->getFormatter(),
);

$this->output = $output;
}









public function write($messages, bool $newline = false, int $options = 0): void
{
$messages = (new Collection($messages))
->filter(fn ($message) => ! Str::contains($message, $this->ignore));

$this->output->write($messages->toArray(), $newline, $options);
}
}
