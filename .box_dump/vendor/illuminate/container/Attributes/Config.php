<?php

namespace Illuminate\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Config implements ContextualAttribute
{



public function __construct(public string $key, public mixed $default = null)
{
}








public static function resolve(self $attribute, Container $container)
{
return $container->make('config')->get($attribute->key, $attribute->default);
}
}
