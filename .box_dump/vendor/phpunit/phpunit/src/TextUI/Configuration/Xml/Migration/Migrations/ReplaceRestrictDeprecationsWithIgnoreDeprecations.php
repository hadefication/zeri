<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class ReplaceRestrictDeprecationsWithIgnoreDeprecations implements Migration
{



public function migrate(DOMDocument $document): void
{
$source = $document->getElementsByTagName('source')->item(0);

if ($source === null) {
return;
}

assert($source instanceof DOMElement);

if (!$source->hasAttribute('restrictDeprecations')) {
return;
}

$restrictDeprecations = $source->getAttribute('restrictDeprecations') === 'true';

$source->removeAttribute('restrictDeprecations');

if (!$restrictDeprecations ||
$source->hasAttribute('ignoreIndirectDeprecations')) {
return;
}

$source->setAttribute('ignoreIndirectDeprecations', 'true');
}
}
