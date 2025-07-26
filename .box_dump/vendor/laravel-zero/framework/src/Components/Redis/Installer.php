<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Redis;

use LaravelZero\Framework\Components\AbstractInstaller;


final class Installer extends AbstractInstaller
{

protected $name = 'install:redis';


protected $description = 'Redis: In-memory data structure';


public function install(): void
{
$this->require('illuminate/redis "^12.17"');
}
}
