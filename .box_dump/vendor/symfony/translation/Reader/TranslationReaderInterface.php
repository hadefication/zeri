<?php










namespace Symfony\Component\Translation\Reader;

use Symfony\Component\Translation\MessageCatalogue;






interface TranslationReaderInterface
{



public function read(string $directory, MessageCatalogue $catalogue): void;
}
