<?php

declare(strict_types=1);

namespace Pest;

use Pest\PendingCalls\UsesCall;

/**
@mixin


*/
final readonly class Configuration
{



private string $filename;




public function __construct(
string $filename,
) {
$this->filename = str_ends_with($filename, DIRECTORY_SEPARATOR.'Pest.php') ? dirname($filename) : $filename;
}




public function in(string ...$targets): UsesCall
{
return (new UsesCall($this->filename, []))->in(...$targets);
}




public function extend(string ...$classAndTraits): UsesCall
{
return new UsesCall(
$this->filename,
array_values($classAndTraits)
);
}




public function extends(string ...$classAndTraits): UsesCall
{
return $this->extend(...$classAndTraits);
}




public function group(string ...$groups): UsesCall
{
return (new UsesCall($this->filename, []))->group(...$groups);
}




public function use(string ...$classAndTraits): UsesCall
{
return $this->extend(...$classAndTraits);
}




public function uses(string ...$classAndTraits): UsesCall
{
return $this->extends(...$classAndTraits);
}




public function printer(): Configuration\Printer
{
return new Configuration\Printer;
}




public function presets(): Configuration\Presets
{
return new Configuration\Presets;
}




public function project(): Configuration\Project
{
return Configuration\Project::getInstance();
}






public function __call(string $name, array $arguments): mixed
{
return $this->uses()->$name(...$arguments); 
}
}
