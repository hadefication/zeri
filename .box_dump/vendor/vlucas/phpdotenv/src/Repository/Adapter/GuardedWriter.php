<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

final class GuardedWriter implements WriterInterface
{





private $writer;






private $allowList;









public function __construct(WriterInterface $writer, array $allowList)
{
$this->writer = $writer;
$this->allowList = $allowList;
}









public function write(string $name, string $value)
{

if (!$this->isAllowed($name)) {
return false;
}


return $this->writer->write($name, $value);
}








public function delete(string $name)
{

if (!$this->isAllowed($name)) {
return false;
}


return $this->writer->delete($name);
}








private function isAllowed(string $name)
{
return \in_array($name, $this->allowList, true);
}
}
