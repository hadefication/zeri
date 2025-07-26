<?php

declare(strict_types=1);










namespace LaravelZero\Framework;

use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Exceptions\ConsoleException;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class Application extends BaseApplication
{
/**
     * @{@inheritdoc}
     */
public static function configure(?string $basePath = null): ApplicationBuilder
{
$basePath = match (true) {
is_string($basePath) => $basePath,
default => static::inferBasePath(),
};

$builder = (new ApplicationBuilder(new static($basePath))); 

$builder->create()->singleton(
\Illuminate\Contracts\Console\Kernel::class,
\LaravelZero\Framework\Kernel::class
);

$builder->create()->singleton(
\Illuminate\Contracts\Debug\ExceptionHandler::class,
\Illuminate\Foundation\Exceptions\Handler::class
);

return $builder
->withEvents()
->withCommands()
->withProviders();
}




public function buildsPath(string $path = ''): string
{
return $this->basePath('builds'.($path ? DIRECTORY_SEPARATOR.$path : $path));
}




protected function registerBaseBindings(): void
{
parent::registerBaseBindings();




$this->make(PackageManifest::class)->manifest = [];
}




protected function registerBaseServiceProviders(): void
{
$this->register(new EventServiceProvider($this));
}




public function version()
{
return $this['config']->get('app.version');
}




public function runningInConsole(): bool
{
return true;
}




public function isDownForMaintenance(): bool
{
return false;
}




public function configurationIsCached(): bool
{
return false;
}




public function registerConfiguredProviders(): void
{
$providers = Collection::make($this['config']['app.providers'])
->partition(
fn ($provider) => Str::startsWith($provider, 'Illuminate\\')
);

$providers->splice(
1,
0,

[
$this->make(PackageManifest::class)
->providers(),
]
);

(new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))->load(
$providers->collapse()
->toArray()
);
}




public function abort($code, $message = '', array $headers = []): void
{
if ($code === 404) {
throw new CommandNotFoundException($message);
}

throw new ConsoleException($code, $message, $headers);
}
}
