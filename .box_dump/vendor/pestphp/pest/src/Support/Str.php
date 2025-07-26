<?php

declare(strict_types=1);

namespace Pest\Support;




final class Str
{




private const POOL = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';




private const PREFIX = '__pest_evaluable_';









public static function random(int $length = 16): string
{
return substr(str_shuffle(str_repeat(self::POOL, 5)), 0, $length);
}




public static function startsWith(string $target, string $search): bool
{
return str_starts_with($target, $search);
}




public static function endsWith(string $target, string $search): bool
{
$length = strlen($search);
if ($length === 0) {
return true;
}

return $search === substr($target, -$length);
}




public static function evaluable(string $code): string
{
$code = str_replace('_', '__', $code);

$code = self::PREFIX.str_replace(' ', '_', $code);


return (string) preg_replace('/[^a-zA-Z0-9_\x80-\xff]/', '_', $code);
}




public static function beforeLast(string $subject, string $search): string
{
if ($search === '') {
return $subject;
}

$pos = mb_strrpos($subject, $search);

if ($pos === false) {
return $subject;
}

return substr($subject, 0, $pos);
}




public static function after(string $subject, string $search): string
{
return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
}




public static function isUuid(string $value): bool
{
return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
}






public static function describe(array $describeDescriptions, string $testDescription): string
{
$descriptionComponents = [...$describeDescriptions, $testDescription];

return sprintf(str_repeat('`%s` → ', count($describeDescriptions)).'%s', ...$descriptionComponents);
}




public static function isUrl(string $value): bool
{
return (bool) filter_var($value, FILTER_VALIDATE_URL);
}
}
