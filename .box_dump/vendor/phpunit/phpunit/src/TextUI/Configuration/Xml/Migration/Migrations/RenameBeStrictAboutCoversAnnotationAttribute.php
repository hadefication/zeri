<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RenameBeStrictAboutCoversAnnotationAttribute implements Migration
{
public function migrate(DOMDocument $document): void
{
$root = $document->documentElement;

assert($root instanceof DOMElement);

if ($root->hasAttribute('beStrictAboutCoverageMetadata')) {
return;
}

if (!$root->hasAttribute('beStrictAboutCoversAnnotation')) {
return;
}

$root->setAttribute('beStrictAboutCoverageMetadata', $root->getAttribute('beStrictAboutCoversAnnotation'));
$root->removeAttribute('beStrictAboutCoversAnnotation');
}
}
