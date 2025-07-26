<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

final class ReplacingWriter implements WriterInterface
{





private $writer;






private $reader;






private $seen;









public function __construct(WriterInterface $writer, ReaderInterface $reader)
{
$this->writer = $writer;
$this->reader = $reader;
$this->seen = [];
}









public function write(string $name, string $value)
{
if ($this->exists($name)) {
return $this->writer->write($name, $value);
}


return true;
}








public function delete(string $name)
{
if ($this->exists($name)) {
return $this->writer->delete($name);
}


return true;
}











private function exists(string $name)
{
if (isset($this->seen[$name])) {
return true;
}

if ($this->reader->read($name)->isDefined()) {
$this->seen[$name] = '';

return true;
}

return false;
}
}
