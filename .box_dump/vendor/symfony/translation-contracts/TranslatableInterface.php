<?php










namespace Symfony\Contracts\Translation;




interface TranslatableInterface
{
public function trans(TranslatorInterface $translator, ?string $locale = null): string;
}
