<?php

namespace Illuminate\Support\Facades;















class Pipeline extends Facade
{





protected static $cached = false;






protected static function getFacadeAccessor()
{
return 'pipeline';
}
}
