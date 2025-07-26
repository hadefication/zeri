<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

use PhpOption\Option;
use PhpOption\Some;

final class ArrayAdapter implements AdapterInterface
{





private $variables;






private function __construct()
{
$this->variables = [];
}






public static function create()
{

return Some::create(new self());
}








public function read(string $name)
{
return Option::fromArraysValue($this->variables, $name);
}









public function write(string $name, string $value)
{
$this->variables[$name] = $value;

return true;
}








public function delete(string $name)
{
unset($this->variables[$name]);

return true;
}
}
