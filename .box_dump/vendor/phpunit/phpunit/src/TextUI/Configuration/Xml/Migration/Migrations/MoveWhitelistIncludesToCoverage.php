<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class MoveWhitelistIncludesToCoverage implements Migration
{



public function migrate(DOMDocument $document): void
{
$whitelist = $document->getElementsByTagName('whitelist')->item(0);

if ($whitelist === null) {
return;
}

$coverage = $document->getElementsByTagName('coverage')->item(0);

if (!$coverage instanceof DOMElement) {
throw new MigrationException('Unexpected state - No coverage element');
}

$include = $document->createElement('include');
$coverage->appendChild($include);

foreach (SnapshotNodeList::fromNodeList($whitelist->childNodes) as $child) {
if (!$child instanceof DOMElement) {
continue;
}

if (!($child->nodeName === 'directory' || $child->nodeName === 'file')) {
continue;
}

$include->appendChild($child);
}
}
}
