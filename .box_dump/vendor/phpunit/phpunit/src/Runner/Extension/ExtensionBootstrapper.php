<?php declare(strict_types=1);








namespace PHPUnit\Runner\Extension;

use const PHP_EOL;
use function assert;
use function class_exists;
use function class_implements;
use function in_array;
use function sprintf;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\TextUI\Configuration\Configuration;
use ReflectionClass;
use Throwable;

/**
@no-named-arguments


*/
final readonly class ExtensionBootstrapper
{
private Configuration $configuration;
private Facade $facade;

public function __construct(Configuration $configuration, Facade $facade)
{
$this->configuration = $configuration;
$this->facade = $facade;
}





public function bootstrap(string $className, array $parameters): void
{
if (!class_exists($className)) {
EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
sprintf(
'Cannot bootstrap extension because class %s does not exist',
$className,
),
);

return;
}

if (!in_array(Extension::class, class_implements($className), true)) {
EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
sprintf(
'Cannot bootstrap extension because class %s does not implement interface %s',
$className,
Extension::class,
),
);

return;
}

try {
$instance = (new ReflectionClass($className))->newInstance();

assert($instance instanceof Extension);

$instance->bootstrap(
$this->configuration,
$this->facade,
ParameterCollection::fromArray($parameters),
);
} catch (Throwable $t) {
EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
sprintf(
'Bootstrapping of extension %s failed: %s%s%s',
$className,
$t->getMessage(),
PHP_EOL,
$t->getTraceAsString(),
),
);

return;
}

EventFacade::emitter()->testRunnerBootstrappedExtension(
$className,
$parameters,
);
}
}
