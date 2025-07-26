<?php

namespace Illuminate\Testing\Constraints;

use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

class SeeInOrder extends Constraint
{





protected $content;






protected $failedValue;






public function __construct($content)
{
$this->content = $content;
}







public function matches($values): bool
{
$decodedContent = html_entity_decode($this->content, ENT_QUOTES, 'UTF-8');

$position = 0;

foreach ($values as $value) {
if (empty($value)) {
continue;
}

$decodedValue = html_entity_decode($value, ENT_QUOTES, 'UTF-8');

$valuePosition = mb_strpos($decodedContent, $decodedValue, $position);

if ($valuePosition === false || $valuePosition < $position) {
$this->failedValue = $value;

return false;
}

$position = $valuePosition + mb_strlen($decodedValue);
}

return true;
}







public function failureDescription($values): string
{
return sprintf(
'Failed asserting that \'%s\' contains "%s" in specified order.',
$this->content,
$this->failedValue
);
}






public function toString(): string
{
return (new ReflectionClass($this))->name;
}
}
