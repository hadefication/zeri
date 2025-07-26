<?php










namespace Symfony\Component\Translation\Provider;

use Symfony\Component\Translation\Exception\UnsupportedSchemeException;




class TranslationProviderCollectionFactory
{



public function __construct(
private iterable $factories,
private array $enabledLocales,
) {
}

public function fromConfig(array $config): TranslationProviderCollection
{
$providers = [];
foreach ($config as $name => $currentConfig) {
$providers[$name] = $this->fromDsnObject(
new Dsn($currentConfig['dsn']),
!$currentConfig['locales'] ? $this->enabledLocales : $currentConfig['locales'],
!$currentConfig['domains'] ? [] : $currentConfig['domains']
);
}

return new TranslationProviderCollection($providers);
}

public function fromDsnObject(Dsn $dsn, array $locales, array $domains = []): ProviderInterface
{
foreach ($this->factories as $factory) {
if ($factory->supports($dsn)) {
return new FilteringProvider($factory->create($dsn), $locales, $domains);
}
}

throw new UnsupportedSchemeException($dsn);
}
}
