<?php










namespace Symfony\Component\Translation\Writer;

use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\RuntimeException;
use Symfony\Component\Translation\MessageCatalogue;






class TranslationWriter implements TranslationWriterInterface
{



private array $dumpers = [];




public function addDumper(string $format, DumperInterface $dumper): void
{
$this->dumpers[$format] = $dumper;
}




public function getFormats(): array
{
return array_keys($this->dumpers);
}









public function write(MessageCatalogue $catalogue, string $format, array $options = []): void
{
if (!isset($this->dumpers[$format])) {
throw new InvalidArgumentException(\sprintf('There is no dumper associated with format "%s".', $format));
}


$dumper = $this->dumpers[$format];

if (isset($options['path']) && !is_dir($options['path']) && !@mkdir($options['path'], 0777, true) && !is_dir($options['path'])) {
throw new RuntimeException(\sprintf('Translation Writer was not able to create directory "%s".', $options['path']));
}


$dumper->dump($catalogue, $options);
}
}
