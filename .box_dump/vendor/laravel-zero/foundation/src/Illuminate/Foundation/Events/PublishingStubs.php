<?php

namespace Illuminate\Foundation\Events;

class PublishingStubs
{
use Dispatchable;






public $stubs = [];






public function __construct(array $stubs)
{
$this->stubs = $stubs;
}








public function add(string $path, string $name)
{
$this->stubs[$path] = $name;

return $this;
}
}
