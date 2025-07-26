<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Menu;

use LaravelZero\Framework\Components\AbstractInstaller;




final class Installer extends AbstractInstaller
{



protected $name = 'install:menu';




protected $description = 'Menu: Build beautiful CLI interactive menus';




public function install(): void
{
$this->require('nunomaduro/laravel-console-menu "^3.5"');
}
}
