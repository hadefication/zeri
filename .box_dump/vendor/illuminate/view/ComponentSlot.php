<?php

namespace Illuminate\View;

use Illuminate\Contracts\Support\Htmlable;
use InvalidArgumentException;
use Stringable;

class ComponentSlot implements Htmlable, Stringable
{





public $attributes;






protected $contents;







public function __construct($contents = '', $attributes = [])
{
$this->contents = $contents;

$this->withAttributes($attributes);
}







public function withAttributes(array $attributes)
{
$this->attributes = new ComponentAttributeBag($attributes);

return $this;
}






public function toHtml()
{
return $this->contents;
}






public function isEmpty()
{
return $this->contents === '';
}






public function isNotEmpty()
{
return ! $this->isEmpty();
}







public function hasActualContent(callable|string|null $callable = null)
{
if (is_string($callable) && ! function_exists($callable)) {
throw new InvalidArgumentException('Callable does not exist.');
}

return filter_var(
$this->contents,
FILTER_CALLBACK,
['options' => $callable ?? fn ($input) => trim(preg_replace("/<!--([\s\S]*?)-->/", '', $input))]
) !== '';
}






public function __toString()
{
return $this->toHtml();
}
}
