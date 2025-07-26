<?php

declare(strict_types=1);

namespace Pest;

use NunoMaduro\Collision\Writer;
use Pest\Contracts\Bootstrapper;
use Pest\Exceptions\FatalException;
use Pest\Exceptions\NoDirtyTestsFound;
use Pest\Plugins\Actions\CallsAddsOutput;
use Pest\Plugins\Actions\CallsBoot;
use Pest\Plugins\Actions\CallsHandleArguments;
use Pest\Plugins\Actions\CallsHandleOriginalArguments;
use Pest\Plugins\Actions\CallsTerminable;
use Pest\Support\Container;
use Pest\Support\Reflection;
use Pest\Support\View;
use PHPUnit\TestRunner\TestResult\Facade;
use PHPUnit\TextUI\Application;
use PHPUnit\TextUI\Configuration\Registry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Whoops\Exception\Inspector;




final readonly class Kernel
{





private const BOOTSTRAPPERS = [
Bootstrappers\BootOverrides::class,
Bootstrappers\BootSubscribers::class,
Bootstrappers\BootFiles::class,
Bootstrappers\BootView::class,
Bootstrappers\BootKernelDump::class,
Bootstrappers\BootExcludeList::class,
];




public function __construct(
private Application $application,
private OutputInterface $output,
) {

}




public static function boot(TestSuite $testSuite, InputInterface $input, OutputInterface $output): self
{
$container = Container::getInstance();

$container
->add(TestSuite::class, $testSuite)
->add(InputInterface::class, $input)
->add(OutputInterface::class, $output)
->add(Container::class, $container);

$kernel = new self(
new Application,
$output,
);

register_shutdown_function(fn () => $kernel->shutdown());

foreach (self::BOOTSTRAPPERS as $bootstrapper) {
$bootstrapper = Container::getInstance()->get($bootstrapper);
assert($bootstrapper instanceof Bootstrapper);

$bootstrapper->boot();
}

CallsBoot::execute();

Container::getInstance()->add(self::class, $kernel);

return $kernel;
}







public function handle(array $originalArguments, array $arguments): int
{
CallsHandleOriginalArguments::execute($originalArguments);

$arguments = CallsHandleArguments::execute($arguments);

try {
$this->application->run($arguments);
} catch (NoDirtyTestsFound) {
$this->output->writeln([
'',
'  <fg=white;options=bold;bg=blue> INFO </> No tests found.',
'',
]);
}

$configuration = Registry::get();
$result = Facade::result();

return CallsAddsOutput::execute(
Result::exitCode($configuration, $result),
);
}




public function terminate(): void
{
$preBufferOutput = Container::getInstance()->get(KernelDump::class);

assert($preBufferOutput instanceof KernelDump);

$preBufferOutput->terminate();

CallsTerminable::execute();
}




public function shutdown(): void
{
$this->terminate();

if (is_array($error = error_get_last())) {
if (! in_array($error['type'], [E_ERROR, E_CORE_ERROR], true)) {
return;
}

$message = $error['message'];
$file = $error['file'];
$line = $error['line'];

try {
$writer = new Writer(null, $this->output);

$throwable = new FatalException($message);

Reflection::setPropertyValue($throwable, 'line', $line);
Reflection::setPropertyValue($throwable, 'file', $file);

$inspector = new Inspector($throwable);

$writer->write($inspector);
} catch (Throwable) { 
View::render('components.badge', [
'type' => 'ERROR',
'content' => sprintf('%s in %s:%d', $message, $file, $line),
]);
}

exit(1);
}
}
}
