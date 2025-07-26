<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use DOMElement;

/**
@no-named-arguments


*/
final readonly class CoverageXmlToReport extends LogToReportMigration
{
protected function forType(): string
{
return 'coverage-xml';
}

protected function toReportFormat(DOMElement $logNode): DOMElement
{
$xml = $logNode->ownerDocument->createElement('xml');
$xml->setAttribute('outputDirectory', $logNode->getAttribute('target'));

return $xml;
}
}
