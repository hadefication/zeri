<?php










namespace Symfony\Component\Translation;






interface CatalogueMetadataAwareInterface
{









public function getCatalogueMetadata(string $key = '', string $domain = 'messages'): mixed;




public function setCatalogueMetadata(string $key, mixed $value, string $domain = 'messages'): void;







public function deleteCatalogueMetadata(string $key = '', string $domain = 'messages'): void;
}
