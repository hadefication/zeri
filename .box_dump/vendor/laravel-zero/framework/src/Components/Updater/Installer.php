<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Updater;

use LaravelZero\Framework\Components\AbstractInstaller;




final class Installer extends AbstractInstaller
{



protected $name = 'install:self-update';




protected $description = 'Self-update: Allows to self-update a build application';




public function install(): void
{
$this->require('laravel-zero/phar-updater "^1.4"');
}
}
