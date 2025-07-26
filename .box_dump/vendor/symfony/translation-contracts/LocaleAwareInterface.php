<?php










namespace Symfony\Contracts\Translation;

interface LocaleAwareInterface
{







public function setLocale(string $locale);




public function getLocale(): string;
}
