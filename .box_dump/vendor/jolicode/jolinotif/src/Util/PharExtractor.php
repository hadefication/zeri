<?php










namespace Joli\JoliNotif\Util;




class PharExtractor
{



public static function isLocatedInsideAPhar(string $filePath): bool
{
return str_starts_with($filePath, 'phar://');
}






public static function extractFile(string $filePath, bool $overwrite = false): string
{
$pharPath = \Phar::running(false);

if (!$pharPath) {
return '';
}

$relativeFilePath = substr($filePath, strpos($filePath, $pharPath) + \strlen($pharPath) + 1);
$tmpDir = sys_get_temp_dir() . '/jolinotif';
$extractedFilePath = $tmpDir . '/' . $relativeFilePath;

if (!file_exists($extractedFilePath) || $overwrite) {
$phar = new \Phar($pharPath);
$phar->extractTo($tmpDir, $relativeFilePath, $overwrite);
}

return $extractedFilePath;
}
}
