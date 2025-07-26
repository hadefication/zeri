<?php

namespace Illuminate\Console\View\Components\Mutators;

use Illuminate\Support\Stringable;

class EnsurePunctuation
{






public function __invoke($string)
{
if (! (new Stringable($string))->endsWith(['.', '?', '!', ':'])) {
return "$string.";
}

return $string;
}
}
