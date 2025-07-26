<?php










namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\MessageCatalogue;






class JsonFileDumper extends FileDumper
{
public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string
{
$flags = $options['json_encoding'] ?? \JSON_PRETTY_PRINT;

return json_encode($messages->all($domain), $flags);
}

protected function getExtension(): string
{
return 'json';
}
}
