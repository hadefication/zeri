<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report\Xml;

use DOMElement;




final class Method
{
private readonly DOMElement $contextNode;

public function __construct(DOMElement $context, string $name)
{
$this->contextNode = $context;

$this->setName($name);
}

public function setSignature(string $signature): void
{
$this->contextNode->setAttribute('signature', $signature);
}

public function setLines(string $start, ?string $end = null): void
{
$this->contextNode->setAttribute('start', $start);

if ($end !== null) {
$this->contextNode->setAttribute('end', $end);
}
}

public function setTotals(string $executable, string $executed, string $coverage): void
{
$this->contextNode->setAttribute('executable', $executable);
$this->contextNode->setAttribute('executed', $executed);
$this->contextNode->setAttribute('coverage', $coverage);
}

public function setCrap(string $crap): void
{
$this->contextNode->setAttribute('crap', $crap);
}

private function setName(string $name): void
{
$this->contextNode->setAttribute('name', $name);
}
}
