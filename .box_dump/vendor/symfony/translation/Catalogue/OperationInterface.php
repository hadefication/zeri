<?php










namespace Symfony\Component\Translation\Catalogue;

use Symfony\Component\Translation\MessageCatalogueInterface;




















interface OperationInterface
{



public function getDomains(): array;




public function getMessages(string $domain): array;




public function getNewMessages(string $domain): array;




public function getObsoleteMessages(string $domain): array;




public function getResult(): MessageCatalogueInterface;
}
