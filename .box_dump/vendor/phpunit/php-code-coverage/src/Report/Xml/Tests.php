<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report\Xml;

use function assert;
use DOMElement;

/**
@phpstan-import-type


*/
final class Tests
{
private readonly DOMElement $contextNode;

public function __construct(DOMElement $context)
{
$this->contextNode = $context;
}




public function addTest(string $test, array $result): void
{
$node = $this->contextNode->appendChild(
$this->contextNode->ownerDocument->createElementNS(
'https://schema.phpunit.de/coverage/1.0',
'test',
),
);

assert($node instanceof DOMElement);

$node->setAttribute('name', $test);
$node->setAttribute('size', $result['size']);
$node->setAttribute('status', $result['status']);
}
}
