<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesStyles
{






protected function compileStyle($expression)
{
$expression = is_null($expression) ? '([])' : $expression;

return "style=\"<?php echo \Illuminate\Support\Arr::toCssStyles{$expression} ?>\"";
}
}
