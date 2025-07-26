<?php

declare(strict_types=1);

namespace Termwind;

use DOMDocument;
use DOMNode;
use Termwind\Html\CodeRenderer;
use Termwind\Html\PreRenderer;
use Termwind\Html\TableRenderer;
use Termwind\ValueObjects\Node;




final class HtmlRenderer
{



public function render(string $html, int $options): void
{
$this->parse($html)->render($options);
}




public function parse(string $html): Components\Element
{
$dom = new DOMDocument;

if (strip_tags($html) === $html) {
return Termwind::span($html);
}

$html = '<?xml encoding="UTF-8"><!DOCTYPE html><html><body>'.trim($html).'</body></html>';
$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_COMPACT | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS | LIBXML_NOXMLDECL);


$body = $dom->getElementsByTagName('body')->item(0);
$el = $this->convert(new Node($body));


return is_string($el)
? Termwind::span($el)
: $el;

}




private function convert(Node $node): Components\Element|string
{
$children = [];

if ($node->isName('table')) {
return (new TableRenderer)->toElement($node);
} elseif ($node->isName('code')) {
return (new CodeRenderer)->toElement($node);
} elseif ($node->isName('pre')) {
return (new PreRenderer)->toElement($node);
}

foreach ($node->getChildNodes() as $child) {
$children[] = $this->convert($child);
}

$children = array_filter($children, fn ($child) => $child !== '');

return $this->toElement($node, $children);
}






private function toElement(Node $node, array $children): Components\Element|string
{
if ($node->isText() || $node->isComment()) {
return (string) $node;
}


$properties = [
'isFirstChild' => $node->isFirstChild(),
];

$styles = $node->getClassAttribute();

return match ($node->getName()) {
'body' => $children[0], 
'div' => Termwind::div($children, $styles, $properties),
'p' => Termwind::paragraph($children, $styles, $properties),
'ul' => Termwind::ul($children, $styles, $properties),
'ol' => Termwind::ol($children, $styles, $properties),
'li' => Termwind::li($children, $styles, $properties),
'dl' => Termwind::dl($children, $styles, $properties),
'dt' => Termwind::dt($children, $styles, $properties),
'dd' => Termwind::dd($children, $styles, $properties),
'span' => Termwind::span($children, $styles, $properties),
'br' => Termwind::breakLine($styles, $properties),
'strong' => Termwind::span($children, $styles, $properties)->strong(),
'b' => Termwind::span($children, $styles, $properties)->fontBold(),
'em', 'i' => Termwind::span($children, $styles, $properties)->italic(),
'u' => Termwind::span($children, $styles, $properties)->underline(),
's' => Termwind::span($children, $styles, $properties)->lineThrough(),
'a' => Termwind::anchor($children, $styles, $properties)->href($node->getAttribute('href')),
'hr' => Termwind::hr($styles, $properties),
default => Termwind::div($children, $styles, $properties),
};
}
}
