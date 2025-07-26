<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Error;
use PhpParser\Node\Scalar;

class Int_ extends Scalar {

public const KIND_BIN = 2;
public const KIND_OCT = 8;
public const KIND_DEC = 10;
public const KIND_HEX = 16;


public int $value;







public function __construct(int $value, array $attributes = []) {
$this->attributes = $attributes;
$this->value = $value;
}

public function getSubNodeNames(): array {
return ['value'];
}










public static function fromString(string $str, array $attributes = [], bool $allowInvalidOctal = false): Int_ {
$attributes['rawValue'] = $str;

$str = str_replace('_', '', $str);

if ('0' !== $str[0] || '0' === $str) {
$attributes['kind'] = Int_::KIND_DEC;
return new Int_((int) $str, $attributes);
}

if ('x' === $str[1] || 'X' === $str[1]) {
$attributes['kind'] = Int_::KIND_HEX;
return new Int_(hexdec($str), $attributes);
}

if ('b' === $str[1] || 'B' === $str[1]) {
$attributes['kind'] = Int_::KIND_BIN;
return new Int_(bindec($str), $attributes);
}

if (!$allowInvalidOctal && strpbrk($str, '89')) {
throw new Error('Invalid numeric literal', $attributes);
}


if ('o' === $str[1] || 'O' === $str[1]) {
$str = substr($str, 2);
}


$attributes['kind'] = Int_::KIND_OCT;
return new Int_(intval($str, 8), $attributes);
}

public function getType(): string {
return 'Scalar_Int';
}
}


class_alias(Int_::class, LNumber::class);
