<?php

namespace Illuminate\Support\Facades;
















































class Context extends Facade
{





protected static function getFacadeAccessor()
{
return \Illuminate\Log\Context\Repository::class;
}
}
