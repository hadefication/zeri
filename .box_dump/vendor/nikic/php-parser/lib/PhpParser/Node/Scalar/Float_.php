<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class Float_ extends Scalar {

public float $value;







public function __construct(float $value, array $attributes = []) {
$this->attributes = $attributes;
$this->value = $value;
}

public function getSubNodeNames(): array {
return ['value'];
}




public static function fromString(string $str, array $attributes = []): Float_ {
$attributes['rawValue'] = $str;
$float = self::parse($str);

return new Float_($float, $attributes);
}










public static function parse(string $str): float {
$str = str_replace('_', '', $str);


if ('0' === $str[0]) {

if ('x' === $str[1] || 'X' === $str[1]) {
return hexdec($str);
}


if ('b' === $str[1] || 'B' === $str[1]) {
return bindec($str);
}


if (false === strpbrk($str, '.eE')) {


return octdec(substr($str, 0, strcspn($str, '89')));
}
}


return (float) $str;
}

public function getType(): string {
return 'Scalar_Float';
}
}


class_alias(Float_::class, DNumber::class);
