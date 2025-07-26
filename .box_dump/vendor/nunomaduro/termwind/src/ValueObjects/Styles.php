<?php

declare(strict_types=1);

namespace Termwind\ValueObjects;

use Closure;
use Termwind\Actions\StyleToMethod;
use Termwind\Components\Element;
use Termwind\Components\Hr;
use Termwind\Components\Li;
use Termwind\Components\Ol;
use Termwind\Components\Ul;
use Termwind\Enums\Color;
use Termwind\Exceptions\ColorNotFound;
use Termwind\Exceptions\InvalidStyle;
use Termwind\Repositories\Styles as StyleRepository;

use function Termwind\terminal;




final class Styles
{



public const STYLING_REGEX = "/\<[\w=#\/\;,:.&,%?-]+\>|\\e\[\d+m/";


private array $styles = [];

private ?Element $element = null;









final public function __construct(
private array $properties = [
'colors' => [],
'options' => [],
'isFirstChild' => false,
],
private array $textModifiers = [],
private array $styleModifiers = [],
private array $defaultStyles = []
) {}




public function setElement(Element $element): self
{
$this->element = $element;

return $this;
}






public function defaultStyles(): array
{
return $this->defaultStyles;
}






final public function getProperties(): array
{
return $this->properties;
}






public function setProperties(array $properties): self
{
$this->properties = $properties;

return $this;
}




final public function setStyle(string $style): self
{
$this->styles = array_unique(array_merge($this->styles, [$style]));

return $this;
}




final public function hasStyle(string $style): bool
{
return in_array($style, $this->styles, true);
}




final public function addStyle(string $style): self
{
return StyleToMethod::multiple($this, $style);
}




final public function inheritFromStyles(self $styles): self
{
foreach (['ml', 'mr', 'pl', 'pr', 'width', 'minWidth', 'maxWidth', 'spaceY', 'spaceX'] as $style) {
$this->properties['parentStyles'][$style] = array_merge(
$this->properties['parentStyles'][$style] ?? [],
$styles->properties['parentStyles'][$style] ?? []
);

$this->properties['parentStyles'][$style][] = $styles->properties['styles'][$style] ?? 0;
}

$this->properties['parentStyles']['justifyContent'] = $styles->properties['styles']['justifyContent'] ?? false;

foreach (['bg', 'fg'] as $colorType) {
$value = (array) ($this->properties['colors'][$colorType] ?? []);
$parentValue = (array) ($styles->properties['colors'][$colorType] ?? []);

if ($value === [] && $parentValue !== []) {
$this->properties['colors'][$colorType] = $styles->properties['colors'][$colorType];
}
}

if (! is_null($this->properties['options']['bold'] ?? null) ||
! is_null($styles->properties['options']['bold'] ?? null)) {
$this->properties['options']['bold'] = $this->properties['options']['bold']
?? $styles->properties['options']['bold']
?? false;
}

return $this;
}




final public function bg(string $color, int $variant = 0): self
{
return $this->with(['colors' => [
'bg' => $this->getColorVariant($color, $variant),
]]);
}




final public function fontBold(): self
{
return $this->with(['options' => [
'bold' => true,
]]);
}




final public function fontNormal(): self
{
return $this->with(['options' => [
'bold' => false,
]]);
}




final public function strong(): self
{
$this->styleModifiers[__METHOD__] = static fn ($text): string => sprintf("\e[1m%s\e[0m", $text);

return $this;
}




final public function italic(): self
{
$this->styleModifiers[__METHOD__] = static fn ($text): string => sprintf("\e[3m%s\e[0m", $text);

return $this;
}




final public function underline(): self
{
$this->styleModifiers[__METHOD__] = static fn ($text): string => sprintf("\e[4m%s\e[0m", $text);

return $this;
}




final public function ml(int $margin): self
{
return $this->with(['styles' => [
'ml' => $margin,
]]);
}




final public function mr(int $margin): self
{
return $this->with(['styles' => [
'mr' => $margin,
]]);
}




final public function mb(int $margin): self
{
return $this->with(['styles' => [
'mb' => $margin,
]]);
}




final public function mt(int $margin): self
{
return $this->with(['styles' => [
'mt' => $margin,
]]);
}




final public function mx(int $margin): self
{
return $this->with(['styles' => [
'ml' => $margin,
'mr' => $margin,
]]);
}




final public function my(int $margin): self
{
return $this->with(['styles' => [
'mt' => $margin,
'mb' => $margin,
]]);
}




final public function m(int $margin): self
{
return $this->my($margin)->mx($margin);
}




final public function pl(int $padding): static
{
return $this->with(['styles' => [
'pl' => $padding,
]]);
}




final public function pr(int $padding): static
{
return $this->with(['styles' => [
'pr' => $padding,
]]);
}




final public function px(int $padding): self
{
return $this->pl($padding)->pr($padding);
}




final public function pt(int $padding): static
{
return $this->with(['styles' => [
'pt' => $padding,
]]);
}




final public function pb(int $padding): static
{
return $this->with(['styles' => [
'pb' => $padding,
]]);
}




final public function py(int $padding): self
{
return $this->pt($padding)->pb($padding);
}




final public function p(int $padding): self
{
return $this->pt($padding)->pr($padding)->pb($padding)->pl($padding);
}




final public function spaceY(int $space): self
{
return $this->with(['styles' => [
'spaceY' => $space,
]]);
}




final public function spaceX(int $space): self
{
return $this->with(['styles' => [
'spaceX' => $space,
]]);
}




final public function borderT(int $width = 1): self
{
if (! $this->element instanceof Hr) {
throw new InvalidStyle('`border-t` can only be used on an "hr" element.');
}

$this->styleModifiers[__METHOD__] = function ($text, $styles): string {
$length = $this->getLength($text);
if ($length < 1) {
$margins = (int) ($styles['ml'] ?? 0) + ($styles['mr'] ?? 0);

return str_repeat('─', self::getParentWidth($this->properties['parentStyles'] ?? []) - $margins);
}

return str_repeat('─', $length);
};

return $this;
}




final public function text(string $value, int $variant = 0): self
{
if (in_array($value, ['left', 'right', 'center'], true)) {
return $this->with(['styles' => [
'text-align' => $value,
]]);
}

return $this->with(['colors' => [
'fg' => $this->getColorVariant($value, $variant),
]]);
}




final public function truncate(int $limit = 0, string $end = '…'): self
{
$this->textModifiers[__METHOD__] = function ($text, $styles) use ($limit, $end): string {
$width = $styles['width'] ?? 0;

if (is_string($width)) {
$width = self::calcWidthFromFraction(
$width,
$styles,
$this->properties['parentStyles'] ?? []
);
}

[, $paddingRight, , $paddingLeft] = $this->getPaddings();
$width -= $paddingRight + $paddingLeft;

$limit = $limit > 0 ? $limit : $width;
if ($limit === 0) {
return $text;
}

$limit -= mb_strwidth($end, 'UTF-8');

if ($this->getLength($text) <= $limit) {
return $text;
}

return rtrim(self::trimText($text, $limit).$end);
};

return $this;
}




final public function w(int|string $width): static
{
return $this->with(['styles' => [
'width' => $width,
]]);
}




final public function wFull(): static
{
return $this->w('1/1');
}




final public function wAuto(): static
{
return $this->with(['styles' => [
'width' => null,
]]);
}




final public function minW(int|string $width): static
{
return $this->with(['styles' => [
'minWidth' => $width,
]]);
}




final public function maxW(int|string $width): static
{
return $this->with(['styles' => [
'maxWidth' => $width,
]]);
}




final public function uppercase(): self
{
$this->textModifiers[__METHOD__] = static fn ($text): string => mb_strtoupper($text, 'UTF-8');

return $this;
}




final public function lowercase(): self
{
$this->textModifiers[__METHOD__] = static fn ($text): string => mb_strtolower($text, 'UTF-8');

return $this;
}




final public function capitalize(): self
{
$this->textModifiers[__METHOD__] = static fn ($text): string => mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');

return $this;
}




final public function snakecase(): self
{
$this->textModifiers[__METHOD__] = static fn ($text): string => mb_strtolower(
(string) preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $text),
'UTF-8'
);

return $this;
}




final public function lineThrough(): self
{
$this->styleModifiers[__METHOD__] = static fn ($text): string => sprintf("\e[9m%s\e[0m", $text);

return $this;
}




final public function invisible(): self
{
$this->styleModifiers[__METHOD__] = static fn ($text): string => sprintf("\e[8m%s\e[0m", $text);

return $this;
}




final public function hidden(): self
{
return $this->with(['styles' => [
'display' => 'hidden',
]]);
}




final public function block(): self
{
return $this->with(['styles' => [
'display' => 'block',
]]);
}




final public function flex(): self
{
return $this->with(['styles' => [
'display' => 'flex',
]]);
}




final public function flex1(): self
{
return $this->with(['styles' => [
'flex-1' => true,
]]);
}




final public function justifyBetween(): self
{
return $this->with(['styles' => [
'justifyContent' => 'between',
]]);
}





final public function justifyAround(): self
{
return $this->with(['styles' => [
'justifyContent' => 'around',
]]);
}




final public function justifyEvenly(): self
{
return $this->with(['styles' => [
'justifyContent' => 'evenly',
]]);
}




final public function justifyCenter(): self
{
return $this->with(['styles' => [
'justifyContent' => 'center',
]]);
}




final public function contentRepeat(string $string): self
{
$string = preg_replace("/\[?'?([^'|\]]+)'?\]?/", '$1', $string) ?? '';

$this->textModifiers[__METHOD__] = static fn (): string => str_repeat($string, (int) floor(terminal()->width() / mb_strlen($string, 'UTF-8')));

return $this->with(['styles' => [
'contentRepeat' => true,
]]);
}




final public function prepend(string $string): self
{
$this->textModifiers[__METHOD__] = static fn ($text): string => $string.$text;

return $this;
}




final public function append(string $string): self
{
$this->textModifiers[__METHOD__] = static fn ($text): string => $text.$string;

return $this;
}




final public function list(string $type, int $index = 0): self
{
if (! $this->element instanceof Ul && ! $this->element instanceof Ol && ! $this->element instanceof Li) {
throw new InvalidStyle(sprintf(
'Style list-none cannot be used with %s',
$this->element !== null ? $this->element::class : 'unknown element'
));
}

if (! $this->element instanceof Li) {
return $this;
}

return match ($type) {
'square' => $this->prepend('▪ '),
'disc' => $this->prepend('• '),
'decimal' => $this->prepend(sprintf('%d. ', $index)),
default => $this,
};
}






public function with(array $properties): self
{
$this->properties = array_replace_recursive($this->properties, $properties);

return $this;
}




final public function href(string $href): self
{
$href = str_replace('%', '%%', $href);

return $this->with(['href' => array_filter([$href])]);
}




final public function format(string $content): string
{
foreach ($this->textModifiers as $modifier) {
$content = $modifier(
$content,
$this->properties['styles'] ?? [],
$this->properties['parentStyles'] ?? []
);
}

$content = $this->applyWidth($content);

foreach ($this->styleModifiers as $modifier) {
$content = $modifier($content, $this->properties['styles'] ?? []);
}

return $this->applyStyling($content);
}




private function getFormatString(): string
{
$styles = [];


$href = $this->properties['href'] ?? [];
if ($href !== []) {
$styles[] = sprintf('href=%s', array_pop($href));
}

$colors = $this->properties['colors'] ?? [];

foreach ($colors as $option => $content) {
if (in_array($option, ['fg', 'bg'], true)) {
$content = is_array($content) ? array_pop($content) : $content;

$styles[] = "$option=$content";
}
}

$options = $this->properties['options'] ?? [];

if ($options !== []) {
$options = array_keys(array_filter(
$options, fn ($option) => $option === true
));
$styles[] = count($options) > 0
? 'options='.implode(',', $options)
: 'options=,';
}


if ($styles === []) {
return '%s%s%s%s%s';
}

return '%s<'.implode(';', $styles).'>%s%s%s</>%s';
}






private function getMargins(): array
{
$isFirstChild = (bool) $this->properties['isFirstChild'];

$spaceY = $this->properties['parentStyles']['spaceY'] ?? [];
$spaceY = ! $isFirstChild ? end($spaceY) : 0;

$spaceX = $this->properties['parentStyles']['spaceX'] ?? [];
$spaceX = ! $isFirstChild ? end($spaceX) : 0;

return [
$spaceY > 0 ? $spaceY : $this->properties['styles']['mt'] ?? 0,
$this->properties['styles']['mr'] ?? 0,
$this->properties['styles']['mb'] ?? 0,
$spaceX > 0 ? $spaceX : $this->properties['styles']['ml'] ?? 0,
];
}






private function getPaddings(): array
{
return [
$this->properties['styles']['pt'] ?? 0,
$this->properties['styles']['pr'] ?? 0,
$this->properties['styles']['pb'] ?? 0,
$this->properties['styles']['pl'] ?? 0,
];
}




private function applyWidth(string $content): string
{
$styles = $this->properties['styles'] ?? [];
$minWidth = $styles['minWidth'] ?? -1;
$width = max($styles['width'] ?? -1, $minWidth);
$maxWidth = $styles['maxWidth'] ?? 0;

if ($width < 0) {
return $content;
}

if ($width === 0) {
return '';
}

if (is_string($width)) {
$width = self::calcWidthFromFraction(
$width,
$styles,
$this->properties['parentStyles'] ?? []
);
}

if ($maxWidth > 0) {
$width = min($styles['maxWidth'], $width);
}

$width -= ($styles['pl'] ?? 0) + ($styles['pr'] ?? 0);
$length = $this->getLength($content);

preg_match_all("/\n+/", $content, $matches);


$width *= count($matches[0] ?? []) + 1;
$width += mb_strlen($matches[0][0] ?? '', 'UTF-8');

if ($length <= $width) {
$space = $width - $length;

return match ($styles['text-align'] ?? '') {
'right' => str_repeat(' ', $space).$content,
'center' => str_repeat(' ', (int) floor($space / 2)).$content.str_repeat(' ', (int) ceil($space / 2)),
default => $content.str_repeat(' ', $space),
};
}

return self::trimText($content, $width);
}




private function applyStyling(string $content): string
{
$display = $this->properties['styles']['display'] ?? 'inline';

if ($display === 'hidden') {
return '';
}

$isFirstChild = (bool) $this->properties['isFirstChild'];

[$marginTop, $marginRight, $marginBottom, $marginLeft] = $this->getMargins();
[$paddingTop, $paddingRight, $paddingBottom, $paddingLeft] = $this->getPaddings();

$content = (string) preg_replace('/\r[ \t]?/', "\n",
(string) preg_replace(
'/\n/',
str_repeat(' ', $marginRight + $paddingRight)
."\n".
str_repeat(' ', $marginLeft + $paddingLeft),
$content)
);

$formatted = sprintf(
$this->getFormatString(),
str_repeat(' ', $marginLeft),
str_repeat(' ', $paddingLeft),
$content,
str_repeat(' ', $paddingRight),
str_repeat(' ', $marginRight),
);

$empty = str_replace(
$content,
str_repeat(' ', $this->getLength($content)),
$formatted
);

$items = [];

if (in_array($display, ['block', 'flex'], true) && ! $isFirstChild) {
$items[] = "\n";
}

if ($marginTop > 0) {
$items[] = str_repeat("\n", $marginTop);
}

if ($paddingTop > 0) {
$items[] = $empty."\n";
}

$items[] = $formatted;

if ($paddingBottom > 0) {
$items[] = "\n".$empty;
}

if ($marginBottom > 0) {
$items[] = str_repeat("\n", $marginBottom);
}

return implode('', $items);
}




public function getLength(?string $text = null): int
{
return mb_strlen(preg_replace(
self::STYLING_REGEX,
'',
$text ?? $this->element?->toString() ?? ''
) ?? '', 'UTF-8');
}




public function getInnerWidth(): int
{
$innerLength = $this->getLength();
[, $marginRight, , $marginLeft] = $this->getMargins();

return $innerLength - $marginLeft - $marginRight;
}




private function getColorVariant(string $color, int $variant): string
{
if ($variant > 0) {
$color .= '-'.$variant;
}

if (StyleRepository::has($color)) {
return StyleRepository::get($color)->getColor();
}

$colorConstant = mb_strtoupper(str_replace('-', '_', $color), 'UTF-8');

if (! defined(Color::class."::$colorConstant")) {
throw new ColorNotFound($colorConstant);
}

return constant(Color::class."::$colorConstant");
}







private static function calcWidthFromFraction(string $fraction, array $styles, array $parentStyles): int
{
$width = self::getParentWidth($parentStyles);

preg_match('/(\d+)\/(\d+)/', $fraction, $matches);

if (count($matches) !== 3 || $matches[2] === '0') {
throw new InvalidStyle(sprintf('Style [%s] is invalid.', "w-$fraction"));
}

$width = (int) floor($width * $matches[1] / $matches[2]);
$width -= ($styles['ml'] ?? 0) + ($styles['mr'] ?? 0);

return $width;
}






public static function getParentWidth(array $styles): int
{
$width = terminal()->width();
foreach ($styles['width'] ?? [] as $index => $parentWidth) {
$minWidth = (int) $styles['minWidth'][$index];
$maxWidth = (int) $styles['maxWidth'][$index];
$margins = (int) $styles['ml'][$index] + (int) $styles['mr'][$index];

$parentWidth = max($parentWidth, $minWidth);

if ($parentWidth < 1) {
$parentWidth = $width;
} elseif (is_int($parentWidth)) {
$parentWidth += $margins;
}

preg_match('/(\d+)\/(\d+)/', (string) $parentWidth, $matches);

$width = count($matches) !== 3
? (int) $parentWidth
: (int) floor($width * $matches[1] / $matches[2]);

if ($maxWidth > 0) {
$width = min($maxWidth, $width);
}

$width -= $margins;
$width -= (int) $styles['pl'][$index] + (int) $styles['pr'][$index];
}

return $width;
}





private static function trimText(string $text, int $width): string
{
preg_match_all(self::STYLING_REGEX, $text, $matches, PREG_OFFSET_CAPTURE);
$text = rtrim(mb_strimwidth(preg_replace(self::STYLING_REGEX, '', $text) ?? '', 0, $width, '', 'UTF-8'));


foreach ($matches[0] ?? [] as [$part, $index]) {
$text = substr($text, 0, $index).$part.substr($text, $index, null);
}

return $text;
}
}
