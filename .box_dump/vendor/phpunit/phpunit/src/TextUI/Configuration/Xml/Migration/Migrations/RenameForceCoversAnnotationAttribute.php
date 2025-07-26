<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RenameForceCoversAnnotationAttribute implements Migration
{
public function migrate(DOMDocument $document): void
{
$root = $document->documentElement;

assert($root instanceof DOMElement);

if ($root->hasAttribute('requireCoverageMetadata')) {
return;
}

if (!$root->hasAttribute('forceCoversAnnotation')) {
return;
}

$root->setAttribute('requireCoverageMetadata', $root->getAttribute('forceCoversAnnotation'));
$root->removeAttribute('forceCoversAnnotation');
}
}
