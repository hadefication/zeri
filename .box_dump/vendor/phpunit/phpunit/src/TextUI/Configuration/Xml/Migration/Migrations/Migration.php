<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use DOMDocument;

/**
@no-named-arguments


*/
interface Migration
{
public function migrate(DOMDocument $document): void;
}
