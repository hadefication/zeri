<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Http;

use LaravelZero\Framework\Components\AbstractInstaller;




final class Installer extends AbstractInstaller
{



protected $name = 'install:http';




protected $description = 'Http: Manage web requests using a fluent HTTP client';




public function install(): void
{
$this->require('illuminate/http "^12.17"');
}
}
