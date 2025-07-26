<?php

namespace Illuminate\Support;

if (! function_exists('Illuminate\Support\enum_value')) {
/**
@template
@template








*/
function enum_value($value, $default = null)
{
return match (true) {
$value instanceof \BackedEnum => $value->value,
$value instanceof \UnitEnum => $value->name,

default => $value ?? value($default),
};
}
}
