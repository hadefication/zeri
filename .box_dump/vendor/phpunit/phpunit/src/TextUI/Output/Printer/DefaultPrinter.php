<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output;

use function assert;
use function count;
use function dirname;
use function explode;
use function fclose;
use function fopen;
use function fsockopen;
use function fwrite;
use function str_replace;
use function str_starts_with;
use PHPUnit\Runner\DirectoryDoesNotExistException;
use PHPUnit\TextUI\CannotOpenSocketException;
use PHPUnit\TextUI\InvalidSocketException;
use PHPUnit\Util\Filesystem;

/**
@no-named-arguments


*/
final class DefaultPrinter implements Printer
{



private $stream;
private readonly bool $isPhpStream;
private bool $isOpen;






public static function from(string $out): self
{
return new self($out);
}






public static function standardOutput(): self
{
return new self('php://stdout');
}






public static function standardError(): self
{
return new self('php://stderr');
}






private function __construct(string $out)
{
$this->isPhpStream = str_starts_with($out, 'php://');

if (str_starts_with($out, 'socket://')) {
$tmp = explode(':', str_replace('socket://', '', $out));

if (count($tmp) !== 2) {
throw new InvalidSocketException($out);
}

$stream = @fsockopen($tmp[0], (int) $tmp[1]);

if ($stream === false) {
throw new CannotOpenSocketException($tmp[0], (int) $tmp[1]);
}

$this->stream = $stream;
$this->isOpen = true;

return;
}

if (!$this->isPhpStream && !Filesystem::createDirectory(dirname($out))) {
throw new DirectoryDoesNotExistException(dirname($out));
}

$stream = fopen($out, 'wb');

assert($stream !== false);

$this->stream = $stream;
$this->isOpen = true;
}

public function print(string $buffer): void
{
assert($this->isOpen);

fwrite($this->stream, $buffer);
}

public function flush(): void
{
if ($this->isOpen && $this->isPhpStream) {
fclose($this->stream);

$this->isOpen = false;
}
}
}
