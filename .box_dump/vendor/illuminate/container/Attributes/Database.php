<?php

namespace Illuminate\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Database implements ContextualAttribute
{



public function __construct(public ?string $connection = null)
{
}








public static function resolve(self $attribute, Container $container)
{
return $container->make('db')->connection($attribute->connection);
}
}
