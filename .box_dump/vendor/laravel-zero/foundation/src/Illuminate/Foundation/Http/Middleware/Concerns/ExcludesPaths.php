<?php

namespace Illuminate\Foundation\Http\Middleware\Concerns;

trait ExcludesPaths
{






protected function inExceptArray($request)
{
foreach ($this->getExcludedPaths() as $except) {
if ($except !== '/') {
$except = trim($except, '/');
}

if ($request->fullUrlIs($except) || $request->is($except)) {
return true;
}
}

return false;
}






public function getExcludedPaths()
{
return $this->except ?? [];
}
}
