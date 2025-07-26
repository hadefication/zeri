<?php

declare(strict_types=1);

namespace Dotenv\Repository;

use Dotenv\Repository\Adapter\AdapterInterface;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\GuardedWriter;
use Dotenv\Repository\Adapter\ImmutableWriter;
use Dotenv\Repository\Adapter\MultiReader;
use Dotenv\Repository\Adapter\MultiWriter;
use Dotenv\Repository\Adapter\ReaderInterface;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Dotenv\Repository\Adapter\WriterInterface;
use InvalidArgumentException;
use PhpOption\Some;
use ReflectionClass;

final class RepositoryBuilder
{



private const DEFAULT_ADAPTERS = [
ServerConstAdapter::class,
EnvConstAdapter::class,
];






private $readers;






private $writers;






private $immutable;






private $allowList;











private function __construct(array $readers = [], array $writers = [], bool $immutable = false, ?array $allowList = null)
{
$this->readers = $readers;
$this->writers = $writers;
$this->immutable = $immutable;
$this->allowList = $allowList;
}






public static function createWithNoAdapters()
{
return new self();
}






public static function createWithDefaultAdapters()
{
$adapters = \iterator_to_array(self::defaultAdapters());

return new self($adapters, $adapters);
}






private static function defaultAdapters()
{
foreach (self::DEFAULT_ADAPTERS as $adapter) {
$instance = $adapter::create();
if ($instance->isDefined()) {
yield $instance->get();
}
}
}








private static function isAnAdapterClass(string $name)
{
if (!\class_exists($name)) {
return false;
}

return (new ReflectionClass($name))->implementsInterface(AdapterInterface::class);
}













public function addReader($reader)
{
if (!(\is_string($reader) && self::isAnAdapterClass($reader)) && !($reader instanceof ReaderInterface)) {
throw new InvalidArgumentException(
\sprintf(
'Expected either an instance of %s or a class-string implementing %s',
ReaderInterface::class,
AdapterInterface::class
)
);
}

$optional = Some::create($reader)->flatMap(static function ($reader) {
return \is_string($reader) ? $reader::create() : Some::create($reader);
});

$readers = \array_merge($this->readers, \iterator_to_array($optional));

return new self($readers, $this->writers, $this->immutable, $this->allowList);
}













public function addWriter($writer)
{
if (!(\is_string($writer) && self::isAnAdapterClass($writer)) && !($writer instanceof WriterInterface)) {
throw new InvalidArgumentException(
\sprintf(
'Expected either an instance of %s or a class-string implementing %s',
WriterInterface::class,
AdapterInterface::class
)
);
}

$optional = Some::create($writer)->flatMap(static function ($writer) {
return \is_string($writer) ? $writer::create() : Some::create($writer);
});

$writers = \array_merge($this->writers, \iterator_to_array($optional));

return new self($this->readers, $writers, $this->immutable, $this->allowList);
}














public function addAdapter($adapter)
{
if (!(\is_string($adapter) && self::isAnAdapterClass($adapter)) && !($adapter instanceof AdapterInterface)) {
throw new InvalidArgumentException(
\sprintf(
'Expected either an instance of %s or a class-string implementing %s',
WriterInterface::class,
AdapterInterface::class
)
);
}

$optional = Some::create($adapter)->flatMap(static function ($adapter) {
return \is_string($adapter) ? $adapter::create() : Some::create($adapter);
});

$readers = \array_merge($this->readers, \iterator_to_array($optional));
$writers = \array_merge($this->writers, \iterator_to_array($optional));

return new self($readers, $writers, $this->immutable, $this->allowList);
}






public function immutable()
{
return new self($this->readers, $this->writers, true, $this->allowList);
}








public function allowList(?array $allowList = null)
{
return new self($this->readers, $this->writers, $this->immutable, $allowList);
}






public function make()
{
$reader = new MultiReader($this->readers);
$writer = new MultiWriter($this->writers);

if ($this->immutable) {
$writer = new ImmutableWriter($writer, $reader);
}

if ($this->allowList !== null) {
$writer = new GuardedWriter($writer, $this->allowList);
}

return new AdapterRepository($reader, $writer);
}
}
