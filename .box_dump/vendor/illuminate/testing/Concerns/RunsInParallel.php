<?php

namespace Illuminate\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Testing\ParallelConsoleOutput;
use PHPUnit\TextUI\Configuration\PhpHandler;
use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait RunsInParallel
{





protected static $applicationResolver;






protected static $runnerResolver;






protected $options;






protected $output;






protected $runner;







public function __construct($options, OutputInterface $output)
{
$this->options = $options;

if ($output instanceof ConsoleOutput) {
$output = new ParallelConsoleOutput($output);
}

$runnerResolver = static::$runnerResolver ?: function ($options, OutputInterface $output) {
$wrapperRunnerClass = class_exists(\ParaTest\WrapperRunner\WrapperRunner::class)
? \ParaTest\WrapperRunner\WrapperRunner::class
: \ParaTest\Runners\PHPUnit\WrapperRunner::class;

return new $wrapperRunnerClass($options, $output);
};

$this->runner = $runnerResolver($options, $output);
}







public static function resolveApplicationUsing($resolver)
{
static::$applicationResolver = $resolver;
}







public static function resolveRunnerUsing($resolver)
{
static::$runnerResolver = $resolver;
}






public function execute(): int
{
$configuration = $this->options instanceof \ParaTest\Options
? $this->options->configuration
: $this->options->configuration();

(new PhpHandler())->handle($configuration->php());

$this->forEachProcess(function () {
ParallelTesting::callSetUpProcessCallbacks();
});

try {
$potentialExitCode = $this->runner->run();
} finally {
$this->forEachProcess(function () {
ParallelTesting::callTearDownProcessCallbacks();
});
}

return $potentialExitCode ?? $this->getExitCode();
}






public function getExitCode(): int
{
return $this->runner->getExitCode();
}







protected function forEachProcess($callback)
{
$processes = $this->options instanceof \ParaTest\Options
? $this->options->processes
: $this->options->processes();

Collection::range(1, $processes)->each(function ($token) use ($callback) {
tap($this->createApplication(), function ($app) use ($callback, $token) {
ParallelTesting::resolveTokenUsing(fn () => $token);

$callback($app);
})->flush();
});
}








protected function createApplication()
{
$applicationResolver = static::$applicationResolver ?: function () {
if (trait_exists(\Tests\CreatesApplication::class)) {
$applicationCreator = new class
{
use \Tests\CreatesApplication;
};

return $applicationCreator->createApplication();
} elseif (file_exists($path = (Application::inferBasePath().'/bootstrap/app.php'))) {
$app = require $path;

$app->make(Kernel::class)->bootstrap();

return $app;
}

throw new RuntimeException('Parallel Runner unable to resolve application.');
};

return $applicationResolver();
}
}
