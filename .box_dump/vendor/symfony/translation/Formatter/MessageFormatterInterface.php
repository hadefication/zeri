<?php










namespace Symfony\Component\Translation\Formatter;





interface MessageFormatterInterface
{







public function format(string $message, string $locale, array $parameters = []): string;
}
