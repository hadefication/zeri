<?php

namespace Illuminate\Testing;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\Constraints\SeeInOrder;
use Illuminate\View\View;
use Stringable;

class TestView implements Stringable
{
use Macroable;






protected $view;






protected $rendered;






public function __construct(View $view)
{
$this->view = $view;
$this->rendered = $view->render();
}








public function assertViewHas($key, $value = null)
{
if (is_array($key)) {
return $this->assertViewHasAll($key);
}

if (is_null($value)) {
PHPUnit::assertTrue(Arr::has($this->view->gatherData(), $key));
} elseif ($value instanceof Closure) {
PHPUnit::assertTrue($value(Arr::get($this->view->gatherData(), $key)));
} elseif ($value instanceof Model) {
PHPUnit::assertTrue($value->is(Arr::get($this->view->gatherData(), $key)));
} elseif ($value instanceof EloquentCollection) {
$actual = Arr::get($this->view->gatherData(), $key);

PHPUnit::assertInstanceOf(EloquentCollection::class, $actual);
PHPUnit::assertSameSize($value, $actual);

$value->each(fn ($item, $index) => PHPUnit::assertTrue($actual->get($index)->is($item)));
} else {
PHPUnit::assertEquals($value, Arr::get($this->view->gatherData(), $key));
}

return $this;
}







public function assertViewHasAll(array $bindings)
{
foreach ($bindings as $key => $value) {
if (is_int($key)) {
$this->assertViewHas($value);
} else {
$this->assertViewHas($key, $value);
}
}

return $this;
}







public function assertViewMissing($key)
{
PHPUnit::assertFalse(Arr::has($this->view->gatherData(), $key));

return $this;
}






public function assertViewEmpty()
{
PHPUnit::assertEmpty($this->rendered);

return $this;
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
}
