<?php










namespace Symfony\Component\Console\Formatter;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Contracts\Service\ResetInterface;




class OutputFormatterStyleStack implements ResetInterface
{



private array $styles = [];

private OutputFormatterStyleInterface $emptyStyle;

public function __construct(?OutputFormatterStyleInterface $emptyStyle = null)
{
$this->emptyStyle = $emptyStyle ?? new OutputFormatterStyle();
$this->reset();
}




public function reset(): void
{
$this->styles = [];
}




public function push(OutputFormatterStyleInterface $style): void
{
$this->styles[] = $style;
}






public function pop(?OutputFormatterStyleInterface $style = null): OutputFormatterStyleInterface
{
if (!$this->styles) {
return $this->emptyStyle;
}

if (null === $style) {
return array_pop($this->styles);
}

foreach (array_reverse($this->styles, true) as $index => $stackedStyle) {
if ($style->apply('') === $stackedStyle->apply('')) {
$this->styles = \array_slice($this->styles, 0, $index);

return $stackedStyle;
}
}

throw new InvalidArgumentException('Incorrectly nested style tag found.');
}




public function getCurrent(): OutputFormatterStyleInterface
{
if (!$this->styles) {
return $this->emptyStyle;
}

return $this->styles[\count($this->styles) - 1];
}




public function setEmptyStyle(OutputFormatterStyleInterface $emptyStyle): static
{
$this->emptyStyle = $emptyStyle;

return $this;
}

public function getEmptyStyle(): OutputFormatterStyleInterface
{
return $this->emptyStyle;
}
}
