<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Testing\PendingCommand;

trait InteractsWithConsole
{





public $mockConsoleOutput = true;






public $expectsOutput;






public $expectedOutput = [];






public $expectedOutputSubstrings = [];






public $unexpectedOutput = [];






public $unexpectedOutputSubstrings = [];






public $expectedTables = [];






public $expectedQuestions = [];






public $expectedChoices = [];








public function artisan($command, $parameters = [])
{
if (! $this->mockConsoleOutput) {
return $this->app[Kernel::class]->call($command, $parameters);
}

return new PendingCommand($this, $this->app, $command, $parameters);
}






protected function withoutMockingConsoleOutput()
{
$this->mockConsoleOutput = false;

$this->app->offsetUnset(OutputStyle::class);

return $this;
}
}
