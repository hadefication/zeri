<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RemoveLogTypes implements Migration
{
public function migrate(DOMDocument $document): void
{
$logging = $document->getElementsByTagName('logging')->item(0);

if (!$logging instanceof DOMElement) {
return;
}

foreach (SnapshotNodeList::fromNodeList($logging->getElementsByTagName('log')) as $logNode) {
assert($logNode instanceof DOMElement);

switch ($logNode->getAttribute('type')) {
case 'json':
case 'tap':
$logging->removeChild($logNode);
}
}
}
}
