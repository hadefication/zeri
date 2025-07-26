<?php

namespace Illuminate\Support\Facades;

use Illuminate\Support\Testing\Fakes\MailFake;


























































class Mail extends Facade
{





public static function fake()
{
$actualMailManager = static::isFake()
? static::getFacadeRoot()->manager
: static::getFacadeRoot();

return tap(new MailFake($actualMailManager), function ($fake) {
static::swap($fake);
});
}






protected static function getFacadeAccessor()
{
return 'mail.manager';
}
}
