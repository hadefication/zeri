<?php

namespace Illuminate\Testing\Fluent;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\Testing\AssertableJsonString;
use PHPUnit\Framework\Assert as PHPUnit;

class AssertableJson implements Arrayable
{
use Concerns\Has,
Concerns\Matching,
Concerns\Debugging,
Concerns\Interaction,
Conditionable,
Macroable,
Tappable;






private $props;






private $path;







protected function __construct(array $props, ?string $path = null)
{
$this->path = $path;
$this->props = $props;
}







protected function dotPath(string $key = ''): string
{
if (is_null($this->path)) {
return $key;
}

return rtrim(implode('.', [$this->path, $key]), '.');
}







protected function prop(?string $key = null)
{
return Arr::get($this->props, $key);
}








protected function scope(string $key, Closure $callback): static
{
$props = $this->prop($key);
$path = $this->dotPath($key);

PHPUnit::assertIsArray($props, sprintf('Property [%s] is not scopeable.', $path));

$scope = new static($props, $path);
$callback($scope);
$scope->interacted();

return $this;
}







public function first(Closure $callback): static
{
$props = $this->prop();

$path = $this->dotPath();

PHPUnit::assertNotEmpty($props, $path === ''
? 'Cannot scope directly onto the first element of the root level because it is empty.'
: sprintf('Cannot scope directly onto the first element of property [%s] because it is empty.', $path)
);

$key = array_keys($props)[0];

$this->interactsWith($key);

return $this->scope($key, $callback);
}







public function each(Closure $callback): static
{
$props = $this->prop();

$path = $this->dotPath();

PHPUnit::assertNotEmpty($props, $path === ''
? 'Cannot scope directly onto each element of the root level because it is empty.'
: sprintf('Cannot scope directly onto each element of property [%s] because it is empty.', $path)
);

foreach (array_keys($props) as $key) {
$this->interactsWith($key);

$this->scope($key, $callback);
}

return $this;
}







public static function fromArray(array $data): static
{
return new static($data);
}







public static function fromAssertableJsonString(AssertableJsonString $json): static
{
return static::fromArray($json->json());
}






public function toArray()
{
return $this->props;
}
}
