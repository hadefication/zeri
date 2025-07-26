<?php










namespace Symfony\Component\Translation;






interface MetadataAwareInterface
{









public function getMetadata(string $key = '', string $domain = 'messages'): mixed;




public function setMetadata(string $key, mixed $value, string $domain = 'messages'): void;







public function deleteMetadata(string $key = '', string $domain = 'messages'): void;
}
