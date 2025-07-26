<?php










namespace Symfony\Component\Translation;

use Symfony\Component\Translation\Exception\InvalidArgumentException;




interface TranslatorBagInterface
{







public function getCatalogue(?string $locale = null): MessageCatalogueInterface;






public function getCatalogues(): array;
}
