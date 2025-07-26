<?php










namespace Symfony\Component\Translation\Catalogue;

use Symfony\Component\Translation\MessageCatalogueInterface;











class TargetOperation extends AbstractOperation
{
protected function processDomain(string $domain): void
{
$this->messages[$domain] = [
'all' => [],
'new' => [],
'obsolete' => [],
];
$intlDomain = $domain.MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

foreach ($this->target->getCatalogueMetadata('', $domain) ?? [] as $key => $value) {
if (null === $this->result->getCatalogueMetadata($key, $domain)) {
$this->result->setCatalogueMetadata($key, $value, $domain);
}
}

foreach ($this->target->getCatalogueMetadata('', $intlDomain) ?? [] as $key => $value) {
if (null === $this->result->getCatalogueMetadata($key, $intlDomain)) {
$this->result->setCatalogueMetadata($key, $value, $intlDomain);
}
}










foreach ($this->source->all($domain) as $id => $message) {
if ($this->target->has($id, $domain)) {
$this->messages[$domain]['all'][$id] = $message;
$d = $this->source->defines($id, $intlDomain) ? $intlDomain : $domain;
$this->result->add([$id => $message], $d);
if (null !== $keyMetadata = $this->source->getMetadata($id, $d)) {
$this->result->setMetadata($id, $keyMetadata, $d);
}
} else {
$this->messages[$domain]['obsolete'][$id] = $message;
}
}

foreach ($this->target->all($domain) as $id => $message) {
if (!$this->source->has($id, $domain)) {
$this->messages[$domain]['all'][$id] = $message;
$this->messages[$domain]['new'][$id] = $message;
$d = $this->target->defines($id, $intlDomain) ? $intlDomain : $domain;
$this->result->add([$id => $message], $d);
if (null !== $keyMetadata = $this->target->getMetadata($id, $d)) {
$this->result->setMetadata($id, $keyMetadata, $d);
}
}
}
}
}
