<?php

namespace Illuminate\Support;

class HigherOrderWhenProxy
{





protected $target;






protected $condition;






protected $hasCondition = false;






protected $negateConditionOnCapture;






public function __construct($target)
{
$this->target = $target;
}







public function condition($condition)
{
[$this->condition, $this->hasCondition] = [$condition, true];

return $this;
}






public function negateConditionOnCapture()
{
$this->negateConditionOnCapture = true;

return $this;
}







public function __get($key)
{
if (! $this->hasCondition) {
$condition = $this->target->{$key};

return $this->condition($this->negateConditionOnCapture ? ! $condition : $condition);
}

return $this->condition
? $this->target->{$key}
: $this->target;
}








public function __call($method, $parameters)
{
if (! $this->hasCondition) {
$condition = $this->target->{$method}(...$parameters);

return $this->condition($this->negateConditionOnCapture ? ! $condition : $condition);
}

return $this->condition
? $this->target->{$method}(...$parameters)
: $this->target;
}
}
