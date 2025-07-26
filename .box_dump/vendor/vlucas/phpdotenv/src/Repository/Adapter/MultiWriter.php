<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

final class MultiWriter implements WriterInterface
{





private $writers;








public function __construct(array $writers)
{
$this->writers = $writers;
}









public function write(string $name, string $value)
{
foreach ($this->writers as $writers) {
if (!$writers->write($name, $value)) {
return false;
}
}

return true;
}








public function delete(string $name)
{
foreach ($this->writers as $writers) {
if (!$writers->delete($name)) {
return false;
}
}

return true;
}
}
