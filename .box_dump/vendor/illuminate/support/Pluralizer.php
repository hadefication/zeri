<?php

namespace Illuminate\Support;

use Doctrine\Inflector\InflectorFactory;

class Pluralizer
{





protected static $inflector;






protected static $language = 'english';








public static $uncountable = [
'recommended',
'related',
];








public static function plural($value, $count = 2)
{
if (is_countable($count)) {
$count = count($count);
}

if ((int) abs($count) === 1 || static::uncountable($value) || preg_match('/^(.*)[A-Za-z0-9\x{0080}-\x{FFFF}]$/u', $value) == 0) {
return $value;
}

$plural = static::inflector()->pluralize($value);

return static::matchCase($plural, $value);
}







public static function singular($value)
{
$singular = static::inflector()->singularize($value);

return static::matchCase($singular, $value);
}







protected static function uncountable($value)
{
return in_array(strtolower($value), static::$uncountable);
}








protected static function matchCase($value, $comparison)
{
$functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];

foreach ($functions as $function) {
if ($function($comparison) === $comparison) {
return $function($value);
}
}

return $value;
}






public static function inflector()
{
if (is_null(static::$inflector)) {
static::$inflector = InflectorFactory::createForLanguage(static::$language)->build();
}

return static::$inflector;
}







public static function useLanguage(string $language)
{
static::$language = $language;

static::$inflector = null;
}
}
