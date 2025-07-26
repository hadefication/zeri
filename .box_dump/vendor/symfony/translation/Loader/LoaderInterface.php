<?php










namespace Symfony\Component\Translation\Loader;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\MessageCatalogue;






interface LoaderInterface
{






public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue;
}
