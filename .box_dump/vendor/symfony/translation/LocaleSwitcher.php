<?php










namespace Symfony\Component\Translation;

use Symfony\Component\Routing\RequestContext;
use Symfony\Contracts\Translation\LocaleAwareInterface;




class LocaleSwitcher implements LocaleAwareInterface
{
private string $defaultLocale;




public function __construct(
private string $locale,
private iterable $localeAwareServices,
private ?RequestContext $requestContext = null,
) {
$this->defaultLocale = $locale;
}

public function setLocale(string $locale): void
{

try {
if (class_exists(\Locale::class, false)) {
\Locale::setDefault($locale);
}
} catch (\Exception) {
}

$this->locale = $locale;
$this->requestContext?->setParameter('_locale', $locale);

foreach ($this->localeAwareServices as $service) {
$service->setLocale($locale);
}
}

public function getLocale(): string
{
return $this->locale;
}

/**
@template






*/
public function runWithLocale(string $locale, callable $callback): mixed
{
$original = $this->getLocale();
$this->setLocale($locale);

try {
return $callback($locale);
} finally {
$this->setLocale($original);
}
}

public function reset(): void
{
$this->setLocale($this->defaultLocale);
}
}
