<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Commands;

use Illuminate\Foundation\Console\TestMakeCommand as BaseTestMakeCommand;

use function ucfirst;

final class TestMakeCommand extends BaseTestMakeCommand
{

protected function getNameInput(): string
{
return ucfirst(parent::getNameInput());
}


protected function getStub(): string
{
$suffix = $this->option('unit') ? '.unit.stub' : '.stub';

$relativePath = $this->usingPest()
? '/stubs/pest'.$suffix
: '/stubs/test'.$suffix;

return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
? $customPath
: __DIR__.$relativePath;
}
}
