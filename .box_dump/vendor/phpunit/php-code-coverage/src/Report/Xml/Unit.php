<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report\Xml;

use function assert;
use DOMElement;




final class Unit
{
private readonly DOMElement $contextNode;

public function __construct(DOMElement $context, string $name)
{
$this->contextNode = $context;

$this->setName($name);
}

public function setLines(int $start, int $executable, int $executed): void
{
$this->contextNode->setAttribute('start', (string) $start);
$this->contextNode->setAttribute('executable', (string) $executable);
$this->contextNode->setAttribute('executed', (string) $executed);
}

public function setCrap(float $crap): void
{
$this->contextNode->setAttribute('crap', (string) $crap);
}

public function setNamespace(string $namespace): void
{
$node = $this->contextNode->getElementsByTagNameNS(
'https://schema.phpunit.de/coverage/1.0',
'namespace',
)->item(0);

if (!$node) {
$node = $this->contextNode->appendChild(
$this->contextNode->ownerDocument->createElementNS(
'https://schema.phpunit.de/coverage/1.0',
'namespace',
),
);
}

assert($node instanceof DOMElement);

$node->setAttribute('name', $namespace);
}

public function addMethod(string $name): Method
{
$node = $this->contextNode->appendChild(
$this->contextNode->ownerDocument->createElementNS(
'https://schema.phpunit.de/coverage/1.0',
'method',
),
);

return new Method($node, $name);
}

private function setName(string $name): void
{
$this->contextNode->setAttribute('name', $name);
}
}
