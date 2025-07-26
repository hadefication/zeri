<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\Build;

use Phar;

use function dirname;




class Build
{





private $environmentFile = '.env';




public function isRunning(): bool
{
return Phar::running() !== '';
}




public function getDirectoryPath(): string
{
return dirname($this->getPath());
}




public function getPath(): string
{
return Phar::running(false);
}




public function environmentFilePath(): string
{
return $this->getDirectoryPath().DIRECTORY_SEPARATOR.$this->environmentFile;
}




public function shouldUseEnvironmentFile(): bool
{
return $this->isRunning() && file_exists($this->environmentFilePath());
}




public function environmentFile(): string
{
return $this->environmentFile;
}
}
