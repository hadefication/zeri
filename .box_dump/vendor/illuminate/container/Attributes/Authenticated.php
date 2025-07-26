<?php

namespace Illuminate\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Authenticated implements ContextualAttribute
{



public function __construct(public ?string $guard = null)
{
}








public static function resolve(self $attribute, Container $container)
{
return call_user_func($container->make('auth')->userResolver(), $attribute->guard);
}
}
