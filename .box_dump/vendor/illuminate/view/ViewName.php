<?php

namespace Illuminate\View;

class ViewName
{






public static function normalize($name)
{
$delimiter = ViewFinderInterface::HINT_PATH_DELIMITER;

if (! str_contains($name, $delimiter)) {
return str_replace('/', '.', $name);
}

[$namespace, $name] = explode($delimiter, $name);

return $namespace.$delimiter.str_replace('/', '.', $name);
}
}
