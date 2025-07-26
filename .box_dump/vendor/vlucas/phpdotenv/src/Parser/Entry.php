<?php

declare(strict_types=1);

namespace Dotenv\Parser;

use PhpOption\Option;

final class Entry
{





private $name;






private $value;









public function __construct(string $name, ?Value $value = null)
{
$this->name = $name;
$this->value = $value;
}






public function getName()
{
return $this->name;
}






public function getValue()
{

return Option::fromValue($this->value);
}
}
