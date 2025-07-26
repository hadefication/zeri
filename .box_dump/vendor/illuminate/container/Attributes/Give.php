<?php

namespace Illuminate\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Give implements ContextualAttribute
{
/**
@template





*/
public function __construct(
public string $class,
public array $params = []
) {
}








public static function resolve(self $attribute, Container $container): mixed
{
return $container->make($attribute->class, $attribute->params);
}
}
