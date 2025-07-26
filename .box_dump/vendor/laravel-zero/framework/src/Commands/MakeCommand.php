<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Commands;

use Illuminate\Foundation\Console\ConsoleMakeCommand;

use function ucfirst;

final class MakeCommand extends ConsoleMakeCommand
{



protected $description = 'Create a new command';




protected function getNameInput(): string
{
return ucfirst(parent::getNameInput());
}


protected function getStub(): string
{
$relativePath = '/stubs/console.stub';

return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
? $customPath
: __DIR__.$relativePath;
}




protected function getDefaultNamespace($rootNamespace): string
{
return $rootNamespace.'\Commands';
}
}
