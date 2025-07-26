<?php

namespace Illuminate\View\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentSlot;

trait ManagesComponents
{





protected $componentStack = [];






protected $componentData = [];






protected $currentComponentData = [];






protected $slots = [];






protected $slotStack = [];








public function startComponent($view, array $data = [])
{
if (ob_start()) {
$this->componentStack[] = $view;

$this->componentData[$this->currentComponent()] = $data;

$this->slots[$this->currentComponent()] = [];
}
}








public function startComponentFirst(array $names, array $data = [])
{
$name = Arr::first($names, function ($item) {
return $this->exists($item);
});

$this->startComponent($name, $data);
}






public function renderComponent()
{
$view = array_pop($this->componentStack);

$this->currentComponentData = array_merge(
$previousComponentData = $this->currentComponentData,
$data = $this->componentData()
);

try {
$view = value($view, $data);

if ($view instanceof View) {
return $view->with($data)->render();
} elseif ($view instanceof Htmlable) {
return $view->toHtml();
} else {
return $this->make($view, $data)->render();
}
} finally {
$this->currentComponentData = $previousComponentData;
}
}






protected function componentData()
{
$defaultSlot = new ComponentSlot(trim(ob_get_clean()));

$slots = array_merge([
'__default' => $defaultSlot,
], $this->slots[count($this->componentStack)]);

return array_merge(
$this->componentData[count($this->componentStack)],
['slot' => $defaultSlot],
$this->slots[count($this->componentStack)],
['__laravel_slots' => $slots]
);
}








public function getConsumableComponentData($key, $default = null)
{
if (array_key_exists($key, $this->currentComponentData)) {
return $this->currentComponentData[$key];
}

$currentComponent = count($this->componentStack);

if ($currentComponent === 0) {
return value($default);
}

for ($i = $currentComponent - 1; $i >= 0; $i--) {
$data = $this->componentData[$i] ?? [];

if (array_key_exists($key, $data)) {
return $data[$key];
}
}

return value($default);
}









public function slot($name, $content = null, $attributes = [])
{
if (func_num_args() === 2 || $content !== null) {
$this->slots[$this->currentComponent()][$name] = $content;
} elseif (ob_start()) {
$this->slots[$this->currentComponent()][$name] = '';

$this->slotStack[$this->currentComponent()][] = [$name, $attributes];
}
}






public function endSlot()
{
last($this->componentStack);

$currentSlot = array_pop(
$this->slotStack[$this->currentComponent()]
);

[$currentName, $currentAttributes] = $currentSlot;

$this->slots[$this->currentComponent()][$currentName] = new ComponentSlot(
trim(ob_get_clean()), $currentAttributes
);
}






protected function currentComponent()
{
return count($this->componentStack) - 1;
}






protected function flushComponents()
{
$this->componentStack = [];
$this->componentData = [];
$this->currentComponentData = [];
}
}
