<?php

declare(strict_types=1);










namespace Carbon;

use Symfony\Component\Translation\MessageCatalogueInterface;




interface TranslatorStrongTypeInterface
{
public function getFromCatalogue(MessageCatalogueInterface $catalogue, string $id, string $domain = 'messages');
}
