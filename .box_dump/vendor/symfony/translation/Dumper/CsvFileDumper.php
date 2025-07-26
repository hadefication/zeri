<?php










namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\MessageCatalogue;






class CsvFileDumper extends FileDumper
{
private string $delimiter = ';';
private string $enclosure = '"';

public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string
{
$handle = fopen('php://memory', 'r+');

foreach ($messages->all($domain) as $source => $target) {
fputcsv($handle, [$source, $target], $this->delimiter, $this->enclosure, '\\');
}

rewind($handle);
$output = stream_get_contents($handle);
fclose($handle);

return $output;
}




public function setCsvControl(string $delimiter = ';', string $enclosure = '"'): void
{
$this->delimiter = $delimiter;
$this->enclosure = $enclosure;
}

protected function getExtension(): string
{
return 'csv';
}
}
