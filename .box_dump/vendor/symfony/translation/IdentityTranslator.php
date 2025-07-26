<?php










namespace Symfony\Component\Translation;

use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;






class IdentityTranslator implements TranslatorInterface, LocaleAwareInterface
{
use TranslatorTrait;
}
