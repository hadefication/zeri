<?php









namespace Mockery\Generator;

use function implode;
use function str_replace;

class MockNameBuilder
{



protected static $mockCounter = 0;




protected $parts = [];




public function addPart($part)
{
$this->parts[] = $part;

return $this;
}




public function build()
{
$parts = ['Mockery', static::$mockCounter++];

foreach ($this->parts as $part) {
$parts[] = str_replace('\\', '_', $part);
}

return implode('_', $parts);
}
}
