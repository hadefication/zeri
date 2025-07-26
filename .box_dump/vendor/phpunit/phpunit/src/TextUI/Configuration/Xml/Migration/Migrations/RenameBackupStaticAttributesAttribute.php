<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RenameBackupStaticAttributesAttribute implements Migration
{
public function migrate(DOMDocument $document): void
{
$root = $document->documentElement;

assert($root instanceof DOMElement);

if ($root->hasAttribute('backupStaticProperties')) {
return;
}

if (!$root->hasAttribute('backupStaticAttributes')) {
return;
}

$root->setAttribute('backupStaticProperties', $root->getAttribute('backupStaticAttributes'));
$root->removeAttribute('backupStaticAttributes');
}
}
