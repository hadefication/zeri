<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report\Xml;

use function basename;
use function dirname;
use DOMDocument;




final class Report extends File
{
public function __construct(string $name)
{
$dom = new DOMDocument;
$dom->loadXML('<?xml version="1.0" ?><phpunit xmlns="https://schema.phpunit.de/coverage/1.0"><file /></phpunit>');

$contextNode = $dom->getElementsByTagNameNS(
'https://schema.phpunit.de/coverage/1.0',
'file',
)->item(0);

parent::__construct($contextNode);

$this->setName($name);
}

public function asDom(): DOMDocument
{
return $this->dom();
}

public function functionObject($name): Method
{
$node = $this->contextNode()->appendChild(
$this->dom()->createElementNS(
'https://schema.phpunit.de/coverage/1.0',
'function',
),
);

return new Method($node, $name);
}

public function classObject($name): Unit
{
return $this->unitObject('class', $name);
}

public function traitObject($name): Unit
{
return $this->unitObject('trait', $name);
}

public function source(): Source
{
$source = $this->contextNode()->getElementsByTagNameNS(
'https://schema.phpunit.de/coverage/1.0',
'source',
)->item(0);

if (!$source) {
$source = $this->contextNode()->appendChild(
$this->dom()->createElementNS(
'https://schema.phpunit.de/coverage/1.0',
'source',
),
);
}

return new Source($source);
}

private function setName(string $name): void
{
$this->contextNode()->setAttribute('name', basename($name));
$this->contextNode()->setAttribute('path', dirname($name));
}

private function unitObject(string $tagName, $name): Unit
{
$node = $this->contextNode()->appendChild(
$this->dom()->createElementNS(
'https://schema.phpunit.de/coverage/1.0',
$tagName,
),
);

return new Unit($node, $name);
}
}
