<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

final class ImmutableWriter implements WriterInterface
{





private $writer;






private $reader;






private $loaded;









public function __construct(WriterInterface $writer, ReaderInterface $reader)
{
$this->writer = $writer;
$this->reader = $reader;
$this->loaded = [];
}









public function write(string $name, string $value)
{


if ($this->isExternallyDefined($name)) {
return false;
}


if (!$this->writer->write($name, $value)) {
return false;
}


$this->loaded[$name] = '';

return true;
}








public function delete(string $name)
{

if ($this->isExternallyDefined($name)) {
return false;
}


if (!$this->writer->delete($name)) {
return false;
}


unset($this->loaded[$name]);

return true;
}










private function isExternallyDefined(string $name)
{
return $this->reader->read($name)->isDefined() && !isset($this->loaded[$name]);
}
}
