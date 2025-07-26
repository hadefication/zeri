<?php

namespace Illuminate\Testing;

use Illuminate\Support\Traits\Macroable;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\Constraints\SeeInOrder;
use Stringable;

class TestComponent implements Stringable
{
use Macroable {
__call as macroCall;
}






public $component;






protected $rendered;







public function __construct($component, $view)
{
$this->component = $component;

$this->rendered = $view->render();
}








public function assertSee($value, $escape = true)
{
$value = $escape ? e($value) : $value;

PHPUnit::assertStringContainsString((string) $value, $this->rendered);

return $this;
}








public function assertSeeInOrder(array $values, $escape = true)
{
$values = $escape ? array_map(e(...), $values) : $values;

PHPUnit::assertThat($values, new SeeInOrder($this->rendered));

return $this;
}








public function assertSeeText($value, $escape = true)
{
$value = $escape ? e($value) : $value;

PHPUnit::assertStringContainsString((string) $value, strip_tags($this->rendered));

return $this;
}








public function assertSeeTextInOrder(array $values, $escape = true)
{
$values = $escape ? array_map(e(...), $values) : $values;

PHPUnit::assertThat($values, new SeeInOrder(strip_tags($this->rendered)));

return $this;
}








public function assertDontSee($value, $escape = true)
{
$value = $escape ? e($value) : $value;

PHPUnit::assertStringNotContainsString((string) $value, $this->rendered);

return $this;
}








public function assertDontSeeText($value, $escape = true)
{
$value = $escape ? e($value) : $value;

PHPUnit::assertStringNotContainsString((string) $value, strip_tags($this->rendered));

return $this;
}






public function __toString()
{
return $this->rendered;
}







public function __get($attribute)
{
return $this->component->{$attribute};
}








public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

return $this->component->{$method}(...$parameters);
}
}
