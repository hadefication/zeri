<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesClasses
{






protected function compileClass($expression)
{
$expression = is_null($expression) ? '([])' : $expression;

return "class=\"<?php echo \Illuminate\Support\Arr::toCssClasses{$expression}; ?>\"";
}
}
