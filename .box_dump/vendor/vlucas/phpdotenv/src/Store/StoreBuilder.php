<?php

declare(strict_types=1);

namespace Dotenv\Store;

use Dotenv\Store\File\Paths;

final class StoreBuilder
{



private const DEFAULT_NAME = '.env';






private $paths;






private $names;






private $shortCircuit;






private $fileEncoding;











private function __construct(array $paths = [], array $names = [], bool $shortCircuit = false, ?string $fileEncoding = null)
{
$this->paths = $paths;
$this->names = $names;
$this->shortCircuit = $shortCircuit;
$this->fileEncoding = $fileEncoding;
}






public static function createWithNoNames()
{
return new self();
}






public static function createWithDefaultName()
{
return new self([], [self::DEFAULT_NAME]);
}








public function addPath(string $path)
{
return new self(\array_merge($this->paths, [$path]), $this->names, $this->shortCircuit, $this->fileEncoding);
}








public function addName(string $name)
{
return new self($this->paths, \array_merge($this->names, [$name]), $this->shortCircuit, $this->fileEncoding);
}






public function shortCircuit()
{
return new self($this->paths, $this->names, true, $this->fileEncoding);
}








public function fileEncoding(?string $fileEncoding = null)
{
return new self($this->paths, $this->names, $this->shortCircuit, $fileEncoding);
}






public function make()
{
return new FileStore(
Paths::filePaths($this->paths, $this->names),
$this->shortCircuit,
$this->fileEncoding
);
}
}
