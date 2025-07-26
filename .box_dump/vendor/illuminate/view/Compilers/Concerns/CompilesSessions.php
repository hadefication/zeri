<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesSessions
{






protected function compileSession($expression)
{
$expression = $this->stripParentheses($expression);

return '<?php $__sessionArgs = ['.$expression.'];
if (session()->has($__sessionArgs[0])) :
if (isset($value)) { $__sessionPrevious[] = $value; }
$value = session()->get($__sessionArgs[0]); ?>';
}







protected function compileEndsession($expression)
{
return '<?php unset($value);
if (isset($__sessionPrevious) && !empty($__sessionPrevious)) { $value = array_pop($__sessionPrevious); }
if (isset($__sessionPrevious) && empty($__sessionPrevious)) { unset($__sessionPrevious); }
endif;
unset($__sessionArgs); ?>';
}
}
