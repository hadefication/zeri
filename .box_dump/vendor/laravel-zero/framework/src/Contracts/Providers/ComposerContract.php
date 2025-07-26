<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Contracts\Providers;




interface ComposerContract
{



public function require(string $package, bool $dev = false): bool;




public function remove(string $package, bool $dev = false): bool;










public function createProject(string $skeleton, string $projectName, array $options): bool;
}
