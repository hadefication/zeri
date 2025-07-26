<?php

namespace Illuminate\View\Concerns;

use InvalidArgumentException;

trait ManagesFragments
{





protected $fragments = [];






protected $fragmentStack = [];







public function startFragment($fragment)
{
if (ob_start()) {
$this->fragmentStack[] = $fragment;
}
}








public function stopFragment()
{
if (empty($this->fragmentStack)) {
throw new InvalidArgumentException('Cannot end a fragment without first starting one.');
}

$last = array_pop($this->fragmentStack);

$this->fragments[$last] = ob_get_clean();

return $this->fragments[$last];
}








public function getFragment($name, $default = null)
{
return $this->getFragments()[$name] ?? $default;
}






public function getFragments()
{
return $this->fragments;
}






public function flushFragments()
{
$this->fragments = [];
$this->fragmentStack = [];
}
}
