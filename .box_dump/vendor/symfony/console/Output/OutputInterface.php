<?php










namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;








interface OutputInterface
{
public const VERBOSITY_SILENT = 8;
public const VERBOSITY_QUIET = 16;
public const VERBOSITY_NORMAL = 32;
public const VERBOSITY_VERBOSE = 64;
public const VERBOSITY_VERY_VERBOSE = 128;
public const VERBOSITY_DEBUG = 256;

public const OUTPUT_NORMAL = 1;
public const OUTPUT_RAW = 2;
public const OUTPUT_PLAIN = 4;








public function write(string|iterable $messages, bool $newline = false, int $options = 0): void;







public function writeln(string|iterable $messages, int $options = 0): void;






public function setVerbosity(int $level): void;






public function getVerbosity(): int;




public function isQuiet(): bool;




public function isVerbose(): bool;




public function isVeryVerbose(): bool;




public function isDebug(): bool;




public function setDecorated(bool $decorated): void;




public function isDecorated(): bool;

public function setFormatter(OutputFormatterInterface $formatter): void;




public function getFormatter(): OutputFormatterInterface;
}
