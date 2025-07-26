<?php

declare(strict_types=1);










namespace NunoMaduro\LaravelConsoleTask;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;






class LaravelConsoleTaskServiceProvider extends ServiceProvider
{



public function boot()
{









Command::macro(
'task',
function (string $title, $task = null, $loadingText = 'loading...') {
$this->output->write("$title: <comment>{$loadingText}</comment>");

if ($task === null) {
$result = true;
} else {
try {
$result = $task() === false ? false : true;
} catch (\Exception $taskException) {
$result = false;
}
}

if ($this->output->isDecorated()) { 

$this->output->write("\x0D");


$this->output->write("\x1B[2K");
} else {
$this->output->writeln(''); 
}

$this->output->writeln(
"$title: ".($result ? '<info>✔</info>' : '<error>failed</error>')
);

if (isset($taskException)) {
throw $taskException;
}

return $result;
}
);
}
}
