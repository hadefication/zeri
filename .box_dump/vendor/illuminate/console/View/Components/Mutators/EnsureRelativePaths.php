<?php

namespace Illuminate\Console\View\Components\Mutators;

class EnsureRelativePaths
{






public function __invoke($string)
{
if (function_exists('app') && app()->has('path.base')) {
$string = str_replace(base_path().'/', '', $string);
}

return $string;
}
}
