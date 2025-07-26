<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
@no-named-arguments


*/
final readonly class RemoveLoggingElements implements Migration
{
public function migrate(DOMDocument $document): void
{
$this->removeTestDoxElement($document);
$this->removeTextElement($document);
}

private function removeTestDoxElement(DOMDocument $document): void
{
$nodes = (new DOMXPath($document))->query('logging/testdoxXml');

assert($nodes !== false);

$node = $nodes->item(0);

if (!$node instanceof DOMElement || $node->parentNode === null) {
return;
}

$node->parentNode->removeChild($node);
}

private function removeTextElement(DOMDocument $document): void
{
$nodes = (new DOMXPath($document))->query('logging/text');

assert($nodes !== false);

$node = $nodes->item(0);

if (!$node instanceof DOMElement || $node->parentNode === null) {
return;
}

$node->parentNode->removeChild($node);
}
}
