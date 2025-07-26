<?php

declare(strict_types=1);

namespace Termwind\ValueObjects;

use Closure;
use Termwind\Actions\StyleToMethod;
use Termwind\Exceptions\InvalidColor;




final class Style
{





public function __construct(private Closure $callback, private string $color = '')
{

}




public function apply(string $styles): void
{
$callback = clone $this->callback;

$this->callback = static function (
Styles $formatter,
string|int ...$arguments
) use ($callback, $styles): Styles {
$formatter = $callback($formatter, ...$arguments);

return StyleToMethod::multiple($formatter, $styles);
};
}




public function color(string $color): void
{
if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color) < 1) {
throw new InvalidColor(sprintf('The color %s is invalid.', $color));
}

$this->color = $color;
}




public function getColor(): string
{
return $this->color;
}




public function __invoke(Styles $styles, string|int ...$arguments): Styles
{
return ($this->callback)($styles, ...$arguments);
}
}
