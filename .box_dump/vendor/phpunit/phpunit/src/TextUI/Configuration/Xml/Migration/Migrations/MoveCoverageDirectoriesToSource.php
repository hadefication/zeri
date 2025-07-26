<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
@no-named-arguments


*/
final readonly class MoveCoverageDirectoriesToSource implements Migration
{



public function migrate(DOMDocument $document): void
{
$source = $document->getElementsByTagName('source')->item(0);

if ($source !== null) {
return;
}

$coverage = $document->getElementsByTagName('coverage')->item(0);

if ($coverage === null) {
return;
}

$root = $document->documentElement;

assert($root instanceof DOMElement);

$source = $document->createElement('source');
$root->appendChild($source);

$xpath = new DOMXPath($document);

foreach (['include', 'exclude'] as $element) {
$nodes = $xpath->query('//coverage/' . $element);

assert($nodes !== false);

foreach (SnapshotNodeList::fromNodeList($nodes) as $node) {
$source->appendChild($node);
}
}

if ($coverage->childElementCount !== 0) {
return;
}

assert($coverage->parentNode !== null);

$coverage->parentNode->removeChild($coverage);
}
}
