<?php

declare(strict_types=1);










namespace NunoMaduro\LaravelConsoleSummary\Contracts;

use Illuminate\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;

interface DescriberContract
{




public function describe(Application $application, OutputInterface $output): void;
}
