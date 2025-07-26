<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\PhpVersion;
use PhpParser\Token;


abstract class TokenEmulator {
abstract public function getPhpVersion(): PhpVersion;

abstract public function isEmulationNeeded(string $code): bool;





abstract public function emulate(string $code, array $tokens): array;





abstract public function reverseEmulate(string $code, array $tokens): array;


public function preprocessCode(string $code, array &$patches): string {
return $code;
}
}
