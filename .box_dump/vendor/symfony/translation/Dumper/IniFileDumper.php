<?php










namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\MessageCatalogue;






class IniFileDumper extends FileDumper
{
public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string
{
$output = '';

foreach ($messages->all($domain) as $source => $target) {
$escapeTarget = str_replace('"', '\"', $target);
$output .= $source.'="'.$escapeTarget."\"\n";
}

return $output;
}

protected function getExtension(): string
{
return 'ini';
}
}
