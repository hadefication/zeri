<?php

declare(strict_types=1);










namespace Carbon;

use Carbon\MessageFormatter\MessageFormatterMapper;
use Closure;
use ReflectionException;
use ReflectionFunction;
use Symfony\Component\Translation;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\Loader\ArrayLoader;

abstract class AbstractTranslator extends Translation\Translator
{
public const REGION_CODE_LENGTH = 2;






protected static array $singletons = [];






protected array $messages = [];






protected array $directories = [];




protected bool $initializing = false;






protected array $aliases = [
'me' => 'sr_Latn_ME',
'scr' => 'sh',
];








public static function get(?string $locale = null): static
{
$locale = $locale ?: 'en';
$key = static::class === Translator::class ? $locale : static::class.'|'.$locale;
$count = \count(static::$singletons);


if ($count > 10) {
foreach (\array_slice(array_keys(static::$singletons), 0, $count - 10) as $index) {
unset(static::$singletons[$index]);
}
}

static::$singletons[$key] ??= new static($locale);

return static::$singletons[$key];
}

public function __construct($locale, ?MessageFormatterInterface $formatter = null, $cacheDir = null, $debug = false)
{
$this->initialize($locale, $formatter, $cacheDir, $debug);
}




public function getDirectories(): array
{
return $this->directories;
}








public function setDirectories(array $directories): static
{
$this->directories = $directories;

return $this;
}








public function addDirectory(string $directory): static
{
$this->directories[] = $directory;

return $this;
}








public function removeDirectory(string $directory): static
{
$search = rtrim(strtr($directory, '\\', '/'), '/');

return $this->setDirectories(array_filter(
$this->getDirectories(),
static fn ($item) => rtrim(strtr($item, '\\', '/'), '/') !== $search,
));
}






public function resetMessages(?string $locale = null): bool
{
if ($locale === null) {
$this->messages = [];

return true;
}

$this->assertValidLocale($locale);

foreach ($this->getDirectories() as $directory) {
$data = @include \sprintf('%s/%s.php', rtrim($directory, '\\/'), $locale);

if ($data !== false) {
$this->messages[$locale] = $data;
$this->addResource('array', $this->messages[$locale], $locale);

return true;
}
}

return false;
}








public function getLocalesFiles(string $prefix = ''): array
{
$files = [];

foreach ($this->getDirectories() as $directory) {
$directory = rtrim($directory, '\\/');

foreach (glob("$directory/$prefix*.php") as $file) {
$files[] = $file;
}
}

return array_unique($files);
}









public function getAvailableLocales(string $prefix = ''): array
{
$locales = [];
foreach ($this->getLocalesFiles($prefix) as $file) {
$locales[] = substr($file, strrpos($file, '/') + 1, -4);
}

return array_unique(array_merge($locales, array_keys($this->messages)));
}

protected function translate(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
{
if ($domain === null) {
$domain = 'messages';
}

$catalogue = $this->getCatalogue($locale);
$format = $this instanceof TranslatorStrongTypeInterface
? $this->getFromCatalogue($catalogue, (string) $id, $domain)
: $this->getCatalogue($locale)->get((string) $id, $domain); 

if ($format instanceof Closure) {

try {
$count = (new ReflectionFunction($format))->getNumberOfRequiredParameters();
} catch (ReflectionException) {
$count = 0;
}


return $format(
...array_values($parameters),
...array_fill(0, max(0, $count - \count($parameters)), null)
);
}

return parent::trans($id, $parameters, $domain, $locale);
}








protected function loadMessagesFromFile(string $locale): bool
{
return isset($this->messages[$locale]) || $this->resetMessages($locale);
}









public function setMessages(string $locale, array $messages): static
{
$this->loadMessagesFromFile($locale);
$this->addResource('array', $messages, $locale);
$this->messages[$locale] = array_merge(
$this->messages[$locale] ?? [],
$messages
);

return $this;
}








public function setTranslations(array $messages): static
{
return $this->setMessages($this->getLocale(), $messages);
}





public function getMessages(?string $locale = null): array
{
return $locale === null ? $this->messages : $this->messages[$locale];
}






public function setLocale($locale): void
{
$locale = preg_replace_callback('/[-_]([a-z]{2,}|\d{2,})/', function ($matches) {

$upper = strtoupper($matches[1]);

if ($upper === 'YUE' || $upper === 'ISO' || \strlen($upper) <= static::REGION_CODE_LENGTH) {
return "_$upper";
}

return '_'.ucfirst($matches[1]);
}, strtolower($locale));

$previousLocale = $this->getLocale();

if ($previousLocale === $locale && isset($this->messages[$locale])) {
return;
}

unset(static::$singletons[$previousLocale]);

if ($locale === 'auto') {
$completeLocale = setlocale(LC_TIME, '0');
$locale = preg_replace('/^([^_.-]+).*$/', '$1', $completeLocale);
$locales = $this->getAvailableLocales($locale);

$completeLocaleChunks = preg_split('/[_.-]+/', $completeLocale);

$getScore = static fn ($language) => self::compareChunkLists(
$completeLocaleChunks,
preg_split('/[_.-]+/', $language),
);

usort($locales, static fn ($first, $second) => $getScore($second) <=> $getScore($first));

$locale = $locales[0];
}

if (isset($this->aliases[$locale])) {
$locale = $this->aliases[$locale];
}


if (str_contains($locale, '_') &&
$this->loadMessagesFromFile($macroLocale = preg_replace('/^([^_]+).*$/', '$1', $locale))
) {
parent::setLocale($macroLocale);
}

if (!$this->loadMessagesFromFile($locale) && !$this->initializing) {
return;
}

parent::setLocale($locale);
}






public function __debugInfo()
{
return [
'locale' => $this->getLocale(),
];
}

public function __serialize(): array
{
return [
'locale' => $this->getLocale(),
];
}

public function __unserialize(array $data): void
{
$this->initialize($data['locale'] ?? 'en');
}

private function initialize($locale, ?MessageFormatterInterface $formatter = null, $cacheDir = null, $debug = false): void
{
parent::setLocale($locale);
$this->initializing = true;
$this->directories = [__DIR__.'/Lang'];
$this->addLoader('array', new ArrayLoader());
parent::__construct($locale, new MessageFormatterMapper($formatter), $cacheDir, $debug);
$this->initializing = false;
}

private static function compareChunkLists($referenceChunks, $chunks)
{
$score = 0;

foreach ($referenceChunks as $index => $chunk) {
if (!isset($chunks[$index])) {
$score++;

continue;
}

if (strtolower($chunks[$index]) === strtolower($chunk)) {
$score += 10;
}
}

return $score;
}
}
