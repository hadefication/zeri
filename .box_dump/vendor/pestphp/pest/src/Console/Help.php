<?php

declare(strict_types=1);

namespace Pest\Console;

use Symfony\Component\Console\Output\OutputInterface;




final readonly class Help
{





private const HELP_MESSAGES = [
'<comment>Pest Options:</comment>',
'  <info>--init</info>                      Initialise a standard Pest configuration',
'  <info>--coverage</info>                  Enable coverage and output to standard output',
'  <info>--min=<fg=cyan><N></></info>                   Set the minimum required coverage percentage (<N>), and fail if not met',
'  <info>--group=<fg=cyan><name></></info>              Only runs tests from the specified group(s)',
];




public function __construct(private OutputInterface $output)
{

}




public function __invoke(): void
{
foreach (self::HELP_MESSAGES as $message) {
$this->output->writeln($message);
}
}
}
