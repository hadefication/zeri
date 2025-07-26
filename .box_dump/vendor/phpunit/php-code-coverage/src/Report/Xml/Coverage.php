<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report\Xml;

use DOMElement;
use SebastianBergmann\CodeCoverage\ReportAlreadyFinalizedException;
use XMLWriter;




final class Coverage
{
private readonly XMLWriter $writer;
private readonly DOMElement $contextNode;
private bool $finalized = false;

public function __construct(DOMElement $context, string $line)
{
$this->contextNode = $context;

$this->writer = new XMLWriter;
$this->writer->openMemory();
$this->writer->startElementNS(null, $context->nodeName, 'https://schema.phpunit.de/coverage/1.0');
$this->writer->writeAttribute('nr', $line);
}




public function addTest(string $test): void
{
if ($this->finalized) {
throw new ReportAlreadyFinalizedException;
}

$this->writer->startElement('covered');
$this->writer->writeAttribute('by', $test);
$this->writer->endElement();
}

public function finalize(): void
{
$this->writer->endElement();

$fragment = $this->contextNode->ownerDocument->createDocumentFragment();
$fragment->appendXML($this->writer->outputMemory());

$this->contextNode->parentNode->replaceChild(
$fragment,
$this->contextNode,
);

$this->finalized = true;
}
}
