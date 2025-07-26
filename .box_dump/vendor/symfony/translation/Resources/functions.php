<?php










namespace Symfony\Component\Translation;

if (!\function_exists(t::class)) {



function t(string $message, array $parameters = [], ?string $domain = null): TranslatableMessage
{
return new TranslatableMessage($message, $parameters, $domain);
}
}
