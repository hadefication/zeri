<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Log;

use Illuminate\Contracts\Config\Repository;
use LaravelZero\Framework\Components\AbstractComponentProvider;




final class Provider extends AbstractComponentProvider
{



public function isAvailable(): bool
{
return file_exists($this->app->configPath('logging.php'))
&& $this->app['config']->get('logging.useDefaultProvider', true) === true;
}




public function register(): void
{
$this->app->register(\Illuminate\Log\LogServiceProvider::class);


$config = $this->app['config'];

$config->set('logging.default', $config->get('logging.default') ?: 'default');
}
}
