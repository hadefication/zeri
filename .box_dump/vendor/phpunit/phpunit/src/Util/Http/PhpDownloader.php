<?php declare(strict_types=1);








namespace PHPUnit\Util\Http;

use function file_get_contents;

/**
@no-named-arguments




*/
final class PhpDownloader implements Downloader
{



public function download(string $url): false|string
{
return file_get_contents($url);
}
}
