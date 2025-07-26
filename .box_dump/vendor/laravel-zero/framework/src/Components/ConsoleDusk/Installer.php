<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\ConsoleDusk;

use LaravelZero\Framework\Components\AbstractInstaller;




final class Installer extends AbstractInstaller
{



protected $name = 'install:console-dusk';




protected $description = 'Console Dusk: Browser automation';




public function install(): void
{
$this->require('nunomaduro/laravel-console-dusk');

$this->info('Usage:');
$this->comment(
'
class VisitLaravelZeroCommand extends Command
{
    public function handle()
    {
        $this->browse(function ($browser) {
            $browser->visit("https://laravel-zero.com")
                ->assertSee("100% Open Source");
        });
    }
}
'
);
}
}
