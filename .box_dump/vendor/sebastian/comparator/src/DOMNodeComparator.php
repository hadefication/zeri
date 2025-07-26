<?php declare(strict_types=1);








namespace SebastianBergmann\Comparator;

use function assert;
use function mb_strtolower;
use function sprintf;
use DOMDocument;
use DOMNode;
use ValueError;

final class DOMNodeComparator extends ObjectComparator
{
public function accepts(mixed $expected, mixed $actual): bool
{
return $expected instanceof DOMNode && $actual instanceof DOMNode;
}






public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false, array &$processed = []): void
{
assert($expected instanceof DOMNode);
assert($actual instanceof DOMNode);

$expectedAsString = $this->nodeToText($expected, true, $ignoreCase);
$actualAsString = $this->nodeToText($actual, true, $ignoreCase);

if ($expectedAsString !== $actualAsString) {
$type = $expected instanceof DOMDocument ? 'documents' : 'nodes';

throw new ComparisonFailure(
$expected,
$actual,
$expectedAsString,
$actualAsString,
sprintf("Failed asserting that two DOM %s are equal.\n", $type),
);
}
}





private function nodeToText(DOMNode $node, bool $canonicalize, bool $ignoreCase): string
{
if ($canonicalize) {
$document = new DOMDocument;

try {
$c14n = $node->C14N();

assert(!empty($c14n));

@$document->loadXML($c14n);
} catch (ValueError) {
}

$node = $document;
}

if ($node instanceof DOMDocument) {
$document = $node;
} else {
$document = $node->ownerDocument;
}

assert($document instanceof DOMDocument);

$document->formatOutput = true;
$document->normalizeDocument();

if ($node instanceof DOMDocument) {
$text = $node->saveXML();
} else {
$text = $document->saveXML($node);
}

assert($text !== false);

if ($ignoreCase) {
return mb_strtolower($text, 'UTF-8');
}

return $text;
}
}
