<?php










namespace Symfony\Component\Translation\Provider;

use Symfony\Component\Translation\TranslatorBag;
use Symfony\Component\Translation\TranslatorBagInterface;

interface ProviderInterface extends \Stringable
{






public function write(TranslatorBagInterface $translatorBag): void;

public function read(array $domains, array $locales): TranslatorBag;

public function delete(TranslatorBagInterface $translatorBag): void;
}
