<?php

declare(strict_types=1);

namespace Termwind\ValueObjects;

use Generator;




final class Node
{



public function __construct(private \DOMNode $node) {}




public function getValue(): string
{
return $this->node->nodeValue ?? '';
}






public function getChildNodes(): Generator
{
foreach ($this->node->childNodes as $node) {
yield new self($node);
}
}




public function isText(): bool
{
return $this->node instanceof \DOMText;
}




public function isComment(): bool
{
return $this->node instanceof \DOMComment;
}




public function isName(string $name): bool
{
return $this->getName() === $name;
}




public function getName(): string
{
return $this->node->nodeName;
}




public function getClassAttribute(): string
{
return $this->getAttribute('class');
}




public function getAttribute(string $name): string
{
if ($this->node instanceof \DOMElement) {
return $this->node->getAttribute($name);
}

return '';
}




public function isEmpty(): bool
{
return $this->isText() && preg_replace('/\s+/', '', $this->getValue()) === '';
}




public function getPreviousSibling(): ?static
{
$node = $this->node;

while ($node = $node->previousSibling) {
$node = new self($node);

if ($node->isEmpty()) {
$node = $node->node;

continue;
}

if (! $node->isComment()) {
return $node;
}

$node = $node->node;
}

return is_null($node) ? null : new self($node);
}




public function getNextSibling(): ?static
{
$node = $this->node;

while ($node = $node->nextSibling) {
$node = new self($node);

if ($node->isEmpty()) {
$node = $node->node;

continue;
}

if (! $node->isComment()) {
return $node;
}

$node = $node->node;
}

return is_null($node) ? null : new self($node);
}




public function isFirstChild(): bool
{
return is_null($this->getPreviousSibling());
}




public function getHtml(): string
{
$html = '';
foreach ($this->node->childNodes as $child) {
if ($child->ownerDocument instanceof \DOMDocument) {
$html .= $child->ownerDocument->saveXML($child);
}
}

return html_entity_decode($html);
}




public function __toString(): string
{
if ($this->isComment()) {
return '';
}

if ($this->getValue() === ' ') {
return ' ';
}

if ($this->isEmpty()) {
return '';
}

$text = preg_replace('/\s+/', ' ', $this->getValue()) ?? '';

if (is_null($this->getPreviousSibling())) {
$text = ltrim($text);
}

if (is_null($this->getNextSibling())) {
$text = rtrim($text);
}

return $text;
}
}
