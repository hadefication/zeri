<?php










namespace Symfony\Component\Finder;






class SplFileInfo extends \SplFileInfo
{





public function __construct(
string $file,
private string $relativePath,
private string $relativePathname,
) {
parent::__construct($file);
}






public function getRelativePath(): string
{
return $this->relativePath;
}






public function getRelativePathname(): string
{
return $this->relativePathname;
}

public function getFilenameWithoutExtension(): string
{
$filename = $this->getFilename();

return pathinfo($filename, \PATHINFO_FILENAME);
}






public function getContents(): string
{
set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });
try {
$content = file_get_contents($this->getPathname());
} finally {
restore_error_handler();
}
if (false === $content) {
throw new \RuntimeException($error);
}

return $content;
}
}
