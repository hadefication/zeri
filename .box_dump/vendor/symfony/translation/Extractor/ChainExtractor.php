<?php










namespace Symfony\Component\Translation\Extractor;

use Symfony\Component\Translation\MessageCatalogue;






class ChainExtractor implements ExtractorInterface
{





private array $extractors = [];




public function addExtractor(string $format, ExtractorInterface $extractor): void
{
$this->extractors[$format] = $extractor;
}

public function setPrefix(string $prefix): void
{
foreach ($this->extractors as $extractor) {
$extractor->setPrefix($prefix);
}
}

public function extract(string|iterable $directory, MessageCatalogue $catalogue): void
{
foreach ($this->extractors as $extractor) {
$extractor->extract($directory, $catalogue);
}
}
}
