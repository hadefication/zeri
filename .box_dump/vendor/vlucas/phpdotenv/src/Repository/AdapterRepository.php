<?php

declare(strict_types=1);

namespace Dotenv\Repository;

use Dotenv\Repository\Adapter\ReaderInterface;
use Dotenv\Repository\Adapter\WriterInterface;
use InvalidArgumentException;

final class AdapterRepository implements RepositoryInterface
{





private $reader;






private $writer;









public function __construct(ReaderInterface $reader, WriterInterface $writer)
{
$this->reader = $reader;
$this->writer = $writer;
}








public function has(string $name)
{
return '' !== $name && $this->reader->read($name)->isDefined();
}










public function get(string $name)
{
if ('' === $name) {
throw new InvalidArgumentException('Expected name to be a non-empty string.');
}

return $this->reader->read($name)->getOrElse(null);
}











public function set(string $name, string $value)
{
if ('' === $name) {
throw new InvalidArgumentException('Expected name to be a non-empty string.');
}

return $this->writer->write($name, $value);
}










public function clear(string $name)
{
if ('' === $name) {
throw new InvalidArgumentException('Expected name to be a non-empty string.');
}

return $this->writer->delete($name);
}
}
