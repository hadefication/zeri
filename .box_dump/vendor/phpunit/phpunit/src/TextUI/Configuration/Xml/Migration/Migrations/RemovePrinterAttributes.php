<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RemovePrinterAttributes implements Migration
{
public function migrate(DOMDocument $document): void
{
$root = $document->documentElement;

assert($root instanceof DOMElement);

if ($root->hasAttribute('printerClass')) {
$root->removeAttribute('printerClass');
}

if ($root->hasAttribute('printerFile')) {
$root->removeAttribute('printerFile');
}
}
}
