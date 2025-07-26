<?php declare(strict_types=1);








namespace PHPUnit\Util\Http;

/**
@no-named-arguments


*/
interface Downloader
{



public function download(string $url): false|string;
}
