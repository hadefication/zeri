<?php

declare(strict_types=1);

namespace Termwind\Actions;

use Termwind\Exceptions\StyleNotFound;
use Termwind\Repositories\Styles as StyleRepository;
use Termwind\Terminal;
use Termwind\ValueObjects\Styles;




final class StyleToMethod
{



private const MEDIA_QUERIES_REGEX = "/^(sm|md|lg|xl|2xl)\:(.*)/";




public const MEDIA_QUERY_BREAKPOINTS = [
'sm' => 64,
'md' => 76,
'lg' => 102,
'xl' => 128,
'2xl' => 153,
];




public function __construct(
private Styles $styles,
private string $style,
) {

}




public static function multiple(Styles $styles, string $stylesString): Styles
{
$stylesString = self::sortStyles(array_merge(
$styles->defaultStyles(),
array_filter((array) preg_split('/(?![^\[]*\])\s/', $stylesString))
));

foreach ($stylesString as $style) {
$styles = (new self($styles, $style))->__invoke();
}

return $styles;
}




public function __invoke(string|int ...$arguments): Styles
{
if (StyleRepository::has($this->style)) {
return StyleRepository::get($this->style)($this->styles, ...$arguments);
}

$method = $this->applyMediaQuery($this->style);

if ($method === '') {
return $this->styles;
}

$method = array_filter(
(array) preg_split('/(?![^\[]*\])-/', $method),
fn ($item) => $item !== false
);

$method = array_slice($method, 0, count($method) - count($arguments));

$methodName = implode(' ', $method);
$methodName = ucwords($methodName);
$methodName = lcfirst($methodName);
$methodName = str_replace(' ', '', $methodName);

if ($methodName === '') {
throw StyleNotFound::fromStyle($this->style);
}

if (! method_exists($this->styles, $methodName)) {
$argument = array_pop($method);

$arguments[] = is_numeric($argument) ? (int) $argument : (string) $argument;

return $this->__invoke(...$arguments);
}


return $this->styles
->setStyle($this->style)
->$methodName(...array_reverse($arguments));
}







private static function sortStyles(array $styles): array
{
$keys = array_keys(self::MEDIA_QUERY_BREAKPOINTS);

usort($styles, function ($a, $b) use ($keys) {
$existsA = (bool) preg_match(self::MEDIA_QUERIES_REGEX, $a, $matchesA);
$existsB = (bool) preg_match(self::MEDIA_QUERIES_REGEX, $b, $matchesB);

if ($existsA && ! $existsB) {
return 1;
}

if ($existsA && array_search($matchesA[1], $keys, true) > array_search($matchesB[1], $keys, true)) {
return 1;
}

return -1;
});

return $styles;
}




private function applyMediaQuery(string $method): string
{
$matches = [];
preg_match(self::MEDIA_QUERIES_REGEX, $method, $matches);

if (count($matches) < 1) {
return $method;
}

[, $size, $method] = $matches;

if ((new Terminal)->width() >= self::MEDIA_QUERY_BREAKPOINTS[$size]) {
return $method;
}

return '';
}
}
