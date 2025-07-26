<?php










namespace Symfony\Component\Translation\Loader;

use Symfony\Component\Translation\Exception\NotFoundResourceException;






class CsvFileLoader extends FileLoader
{
private string $delimiter = ';';
private string $enclosure = '"';



private string $escape = '';

protected function loadResource(string $resource): array
{
$messages = [];

try {
$file = new \SplFileObject($resource, 'rb');
} catch (\RuntimeException $e) {
throw new NotFoundResourceException(\sprintf('Error opening file "%s".', $resource), 0, $e);
}

$file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
$file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);

foreach ($file as $data) {
if (false === $data) {
continue;
}

if (!str_starts_with($data[0], '#') && isset($data[1]) && 2 === \count($data)) {
$messages[$data[0]] = $data[1];
}
}

return $messages;
}




public function setCsvControl(string $delimiter = ';', string $enclosure = '"', string $escape = ''): void
{
$this->delimiter = $delimiter;
$this->enclosure = $enclosure;
if ('' !== $escape) {
trigger_deprecation('symfony/translation', '7.2', 'The "escape" parameter of the "%s" method is deprecated. It will be removed in 8.0.', __METHOD__);
}

$this->escape = $escape;
}
}
