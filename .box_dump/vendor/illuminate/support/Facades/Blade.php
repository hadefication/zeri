<?php

namespace Illuminate\Support\Facades;
















































class Blade extends Facade
{





protected static function getFacadeAccessor()
{
return 'blade.compiler';
}
}
