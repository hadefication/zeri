<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

use PhpParser\PhpVersion;








class ReadonlyFunctionTokenEmulator extends KeywordEmulator {
public function getKeywordString(): string {
return 'readonly';
}

public function getKeywordToken(): int {
return \T_READONLY;
}

public function getPhpVersion(): PhpVersion {
return PhpVersion::fromComponents(8, 2);
}

public function reverseEmulate(string $code, array $tokens): array {

return $tokens;
}
}
