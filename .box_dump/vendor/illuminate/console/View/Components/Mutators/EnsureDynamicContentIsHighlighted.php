<?php

namespace Illuminate\Console\View\Components\Mutators;

class EnsureDynamicContentIsHighlighted
{






public function __invoke($string)
{
return preg_replace('/\[([^\]]+)\]/', '<options=bold>[$1]</>', (string) $string);
}
}
