<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:channel')]
class ChannelMakeCommand extends GeneratorCommand
{





protected $name = 'make:channel';






protected $description = 'Create a new channel class';






protected $type = 'Channel';







protected function buildClass($name)
{
return str_replace(
['DummyUser', '{{ userModel }}'],
class_basename($this->userProviderModel()),
parent::buildClass($name)
);
}






protected function getStub()
{
return __DIR__.'/stubs/channel.stub';
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Broadcasting';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the channel already exists'],
];
}
}
