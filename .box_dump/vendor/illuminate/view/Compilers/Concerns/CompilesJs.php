<?php

namespace Illuminate\View\Compilers\Concerns;

use Illuminate\Support\Js;

trait CompilesJs
{






protected function compileJs(string $expression)
{
return sprintf(
"<?php echo \%s::from(%s)->toHtml() ?>",
Js::class, $this->stripParentheses($expression)
);
}
}
