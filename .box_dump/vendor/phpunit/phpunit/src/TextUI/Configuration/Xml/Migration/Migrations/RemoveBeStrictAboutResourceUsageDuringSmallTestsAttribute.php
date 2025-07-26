<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RemoveBeStrictAboutResourceUsageDuringSmallTestsAttribute implements Migration
{
public function migrate(DOMDocument $document): void
{
$root = $document->documentElement;

assert($root instanceof DOMElement);

if ($root->hasAttribute('beStrictAboutResourceUsageDuringSmallTests')) {
$root->removeAttribute('beStrictAboutResourceUsageDuringSmallTests');
}
}
}
