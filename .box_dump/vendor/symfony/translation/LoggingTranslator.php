<?php










namespace Symfony\Component\Translation;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;




class LoggingTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
public function __construct(
private TranslatorInterface&TranslatorBagInterface&LocaleAwareInterface $translator,
private LoggerInterface $logger,
) {
}

public function trans(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
{
$trans = $this->translator->trans($id = (string) $id, $parameters, $domain, $locale);
$this->log($id, $domain, $locale);

return $trans;
}

public function setLocale(string $locale): void
{
$prev = $this->translator->getLocale();
$this->translator->setLocale($locale);
if ($prev === $locale) {
return;
}

$this->logger->debug(\sprintf('The locale of the translator has changed from "%s" to "%s".', $prev, $locale));
}

public function getLocale(): string
{
return $this->translator->getLocale();
}

public function getCatalogue(?string $locale = null): MessageCatalogueInterface
{
return $this->translator->getCatalogue($locale);
}

public function getCatalogues(): array
{
return $this->translator->getCatalogues();
}




public function getFallbackLocales(): array
{
if ($this->translator instanceof Translator || method_exists($this->translator, 'getFallbackLocales')) {
return $this->translator->getFallbackLocales();
}

return [];
}

public function __call(string $method, array $args): mixed
{
return $this->translator->{$method}(...$args);
}




private function log(string $id, ?string $domain, ?string $locale): void
{
$domain ??= 'messages';

$catalogue = $this->translator->getCatalogue($locale);
if ($catalogue->defines($id, $domain)) {
return;
}

if ($catalogue->has($id, $domain)) {
$this->logger->debug('Translation use fallback catalogue.', ['id' => $id, 'domain' => $domain, 'locale' => $catalogue->getLocale()]);
} else {
$this->logger->warning('Translation not found.', ['id' => $id, 'domain' => $domain, 'locale' => $catalogue->getLocale()]);
}
}
}
