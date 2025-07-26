<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RemoveListeners implements Migration
{
public function migrate(DOMDocument $document): void
{
$node = $document->getElementsByTagName('listeners')->item(0);

if (!$node instanceof DOMElement || $node->parentNode === null) {
return;
}

$node->parentNode->removeChild($node);
}
}
