<?php

declare(strict_types=1);










namespace NunoMaduro\LaravelConsoleSummary;

use Illuminate\Contracts\Container\Container;
use NunoMaduro\LaravelConsoleSummary\Contracts\DescriberContract;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SummaryCommand extends ListCommand
{



private const FORMAT = 'txt';




protected $container;




public function __construct(Container $container)
{
parent::__construct('list');

$this->container = $container;
}




protected function execute(InputInterface $input, OutputInterface $output): int
{
if ($input->getOption('format') === static::FORMAT && ! $input->getOption('raw')) {
$this->container[DescriberContract::class]->describe($this->getApplication(), $output);

return 0;
}

return parent::execute($input, $output);
}
}
