<?php










namespace Symfony\Component\Translation\Formatter;






interface IntlFormatterInterface
{





public function formatIntl(string $message, string $locale, array $parameters = []): string;
}
