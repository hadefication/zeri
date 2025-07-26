<?php

declare(strict_types=1);










namespace LaravelZero\Framework;

use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Console\ConfigPublishCommand;
use Illuminate\Foundation\Console\Kernel as BaseKernel;
use LaravelZero\Framework\Providers\CommandRecorder\CommandRecorderRepository;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand;
use ReflectionClass;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Output\OutputInterface;

use function collect;
use function define;
use function defined;
use function get_class;
use function in_array;

class Kernel extends BaseKernel
{





protected $developmentCommands = [
Commands\BuildCommand::class,
Commands\RenameCommand::class,
Commands\MakeCommand::class,
Commands\InstallCommand::class,
Commands\StubPublishCommand::class,
Commands\TestMakeCommand::class,



ConfigPublishCommand::class,
];






protected $developmentOnlyCommands = [
TestCommand::class,
];






protected $hiddenCommands = [
ConfigPublishCommand::class,
];






protected $bootstrappers = [
\LaravelZero\Framework\Bootstrap\CoreBindings::class,
\LaravelZero\Framework\Bootstrap\LoadEnvironmentVariables::class,
\LaravelZero\Framework\Bootstrap\LoadConfiguration::class,
\Illuminate\Foundation\Bootstrap\HandleExceptions::class,
\LaravelZero\Framework\Bootstrap\RegisterFacades::class,
\LaravelZero\Framework\Bootstrap\RegisterProviders::class,
\Illuminate\Foundation\Bootstrap\BootProviders::class,
];




public function __construct(
\Illuminate\Contracts\Foundation\Application $app,
\Illuminate\Contracts\Events\Dispatcher $events
) {
if (! defined('ARTISAN_BINARY')) {
define('ARTISAN_BINARY', basename($_SERVER['SCRIPT_FILENAME']));
}

parent::__construct($app, $events);
}




public function handle($input, $output = null)
{
$this->app->instance(OutputInterface::class, $output);

if (function_exists('Termwind\renderUsing') && $output) {
\Termwind\renderUsing($output);
}

$this->ensureDefaultCommand($input);

return parent::handle($input, $output);
}







protected function ensureDefaultCommand($input): void
{
$this->bootstrap();

$application = $this->getArtisan();

try {




if ($commandName = $input->getFirstArgument()) {
$application->find($commandName);
}
} catch (CommandNotFoundException $e) {




$application->setDefaultCommand(
resolve(config('commands.default'))->getName(), true
);
}
}




public function bootstrap(): void
{
parent::bootstrap();

if ($commandClass = $this->app['config']->get('commands.default')) {
$this->getArtisan()
->setDefaultCommand(
$this->app[$commandClass]->getName()
);
}
}




protected function commands(): void
{
$config = $this->app['config'];

/**
@phpstan-ignore-next-line


*/
$this->load($config->get('commands.paths', $this->app->path('Commands')));




$commands = collect($config->get('commands.add', []))->merge(
$config->get('commands.hidden', $this->hiddenCommands),
);

if ($command = $config->get('commands.default')) {
$commands->push($command);
}




if ($this->app->environment() !== 'production') {
$commands = $commands->merge($this->developmentCommands);
}

$toRemoveCommands = $config->get('commands.remove', []);

if ($this->app->environment('production')) {
$toRemoveCommands = array_merge($toRemoveCommands, $this->developmentOnlyCommands);
}

$commands = $commands->diff($toRemoveCommands);

Artisan::starting(
function ($artisan) use ($toRemoveCommands) {
$reflectionClass = new ReflectionClass(Artisan::class);
$commands = collect($artisan->all())
->filter(
fn ($command) => ! in_array(get_class($command), $toRemoveCommands, true)
)
->toArray();

$property = $reflectionClass->getParentClass()
->getProperty('commands');

$property->setAccessible(true);
$property->setValue($artisan, $commands);
$property->setAccessible(false);
}
);






Artisan::starting(
function ($artisan) use ($commands) {
$artisan->resolveCommands($commands->toArray());

$artisan->setContainerCommandLoader();
}
);

Artisan::starting(
function ($artisan) use ($config) {
$commands = array_merge(
$config->get('commands.hidden'),
$this->hiddenCommands,
);

collect($artisan->all())->each(
function ($command) use ($artisan, $commands) {
if (in_array(get_class($command), $commands, true)) {
$command->setHidden(true);
}

$command->setApplication($artisan);

if ($command instanceof Commands\Command) {
$this->app->call([$command, 'schedule']);
}
}
);
}
);
}




public function getName(): string
{
return $this->getArtisan()
->getName();
}




public function call($command, array $parameters = [], $outputBuffer = null)
{
if (function_exists('Termwind\renderUsing') && $outputBuffer) {
\Termwind\renderUsing($outputBuffer);
}

resolve(CommandRecorderRepository::class)->create($command, $parameters);

return parent::call($command, $parameters, $outputBuffer);
}
}
