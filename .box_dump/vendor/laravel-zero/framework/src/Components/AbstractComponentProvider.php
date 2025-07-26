<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components;

use Illuminate\Support\ServiceProvider;




abstract class AbstractComponentProvider extends ServiceProvider
{





public function register(): void
{

}




abstract public function isAvailable(): bool;
}
