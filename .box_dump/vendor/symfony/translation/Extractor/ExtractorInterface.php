<?php










namespace Symfony\Component\Translation\Extractor;

use Symfony\Component\Translation\MessageCatalogue;







interface ExtractorInterface
{







public function extract(string|iterable $resource, MessageCatalogue $catalogue);






public function setPrefix(string $prefix);
}
