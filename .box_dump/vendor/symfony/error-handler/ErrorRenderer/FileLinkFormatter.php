<?php










namespace Symfony\Component\ErrorHandler\ErrorRenderer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;








class FileLinkFormatter
{
private array|false $fileLinkFormat;




public function __construct(
string|array|null $fileLinkFormat = null,
private ?RequestStack $requestStack = null,
private ?string $baseDir = null,
private string|\Closure|null $urlFormat = null,
) {
$fileLinkFormat ??= $_ENV['SYMFONY_IDE'] ?? $_SERVER['SYMFONY_IDE'] ?? '';

if (!\is_array($f = $fileLinkFormat)) {
$f = (ErrorRendererInterface::IDE_LINK_FORMATS[$f] ?? $f) ?: \ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format') ?: 'file://%f#L%l';
$i = strpos($f, '&', max(strrpos($f, '%f'), strrpos($f, '%l'))) ?: \strlen($f);
$fileLinkFormat = [substr($f, 0, $i)] + preg_split('/&([^>]++)>/', substr($f, $i), -1, \PREG_SPLIT_DELIM_CAPTURE);
}

$this->fileLinkFormat = $fileLinkFormat;
}

public function format(string $file, int $line): string|false
{
if ($fmt = $this->getFileLinkFormat()) {
for ($i = 1; isset($fmt[$i]); ++$i) {
if (str_starts_with($file, $k = $fmt[$i++])) {
$file = substr_replace($file, $fmt[$i], 0, \strlen($k));
break;
}
}

return strtr($fmt[0], ['%f' => $file, '%l' => $line]);
}

return false;
}




public function __sleep(): array
{
$this->fileLinkFormat = $this->getFileLinkFormat();

return ['fileLinkFormat'];
}




public static function generateUrlFormat(UrlGeneratorInterface $router, string $routeName, string $queryString): ?string
{
try {
return $router->generate($routeName).$queryString;
} catch (\Throwable) {
return null;
}
}

private function getFileLinkFormat(): array|false
{
if ($this->fileLinkFormat) {
return $this->fileLinkFormat;
}

if ($this->requestStack && $this->baseDir && $this->urlFormat) {
$request = $this->requestStack->getMainRequest();

if ($request instanceof Request && (!$this->urlFormat instanceof \Closure || $this->urlFormat = ($this->urlFormat)())) {
return [
$request->getSchemeAndHttpHost().$this->urlFormat,
$this->baseDir.\DIRECTORY_SEPARATOR, '',
];
}
}

return false;
}
}
