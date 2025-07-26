<?php

namespace Illuminate\Support\Facades;

use Illuminate\Support\DateFactory;







































































































class Date extends Facade
{
const DEFAULT_FACADE = DateFactory::class;








protected static function getFacadeAccessor()
{
return 'date';
}







protected static function resolveFacadeInstance($name)
{
if (! isset(static::$resolvedInstance[$name]) && ! isset(static::$app, static::$app[$name])) {
$class = static::DEFAULT_FACADE;

static::swap(new $class);
}

return parent::resolveFacadeInstance($name);
}
}
