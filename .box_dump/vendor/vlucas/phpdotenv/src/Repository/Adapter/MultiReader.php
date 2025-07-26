<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

use PhpOption\None;

final class MultiReader implements ReaderInterface
{





private $readers;








public function __construct(array $readers)
{
$this->readers = $readers;
}








public function read(string $name)
{
foreach ($this->readers as $reader) {
$result = $reader->read($name);
if ($result->isDefined()) {
return $result;
}
}

return None::create();
}
}
