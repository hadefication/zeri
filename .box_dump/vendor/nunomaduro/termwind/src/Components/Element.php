<?php

declare(strict_types=1);

namespace Termwind\Components;

use Symfony\Component\Console\Output\OutputInterface;
use Termwind\Actions\StyleToMethod;
use Termwind\Html\InheritStyles;
use Termwind\ValueObjects\Styles;

















abstract class Element
{

protected static array $defaultStyles = [];

protected Styles $styles;






final public function __construct(
protected OutputInterface $output,
protected array|string $content,
?Styles $styles = null
) {
$this->styles = $styles ?? new Styles(defaultStyles: static::$defaultStyles);
$this->styles->setElement($this);
}







final public static function fromStyles(OutputInterface $output, array|string $content, string $styles = '', array $properties = []): static
{
$element = new static($output, $content);
if ($properties !== []) {
$element->styles->setProperties($properties);
}

$elementStyles = StyleToMethod::multiple($element->styles, $styles);

return new static($output, $content, $elementStyles);
}




public function toString(): string
{
if (is_array($this->content)) {
$inheritance = new InheritStyles;
$this->content = implode('', $inheritance($this->content, $this->styles));
}

return $this->styles->format($this->content);
}




public function __call(string $name, array $arguments): mixed
{
if (method_exists($this->styles, $name)) {

$result = $this->styles->{$name}(...$arguments);

if (str_starts_with($name, 'get') || str_starts_with($name, 'has')) {
return $result;
}
}

return $this;
}






final public function setContent(array|string $content): static
{
return new static($this->output, $content, $this->styles);
}




final public function render(int $options): void
{
$this->output->writeln($this->toString(), $options);
}




final public function __toString(): string
{
return $this->toString();
}
}
