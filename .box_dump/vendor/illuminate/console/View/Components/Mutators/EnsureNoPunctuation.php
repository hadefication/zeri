<?php

namespace Illuminate\Console\View\Components\Mutators;

use Illuminate\Support\Stringable;

class EnsureNoPunctuation
{






public function __invoke($string)
{
if ((new Stringable($string))->endsWith(['.', '?', '!', ':'])) {
return substr_replace($string, '', -1);
}

return $string;
}
}
