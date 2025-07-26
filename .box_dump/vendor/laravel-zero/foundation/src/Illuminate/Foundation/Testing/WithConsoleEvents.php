<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

trait WithConsoleEvents
{





protected function setUpWithConsoleEvents()
{
$this->app[ConsoleKernel::class]->rerouteSymfonyCommandEvents();
}
}
