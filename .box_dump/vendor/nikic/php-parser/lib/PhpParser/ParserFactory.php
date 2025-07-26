<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Parser\Php7;
use PhpParser\Parser\Php8;

class ParserFactory {





public function createForVersion(PhpVersion $version): Parser {
if ($version->isHostVersion()) {
$lexer = new Lexer();
} else {
$lexer = new Lexer\Emulative($version);
}
if ($version->id >= 80000) {
return new Php8($lexer, $version);
}
return new Php7($lexer, $version);
}






public function createForNewestSupportedVersion(): Parser {
return $this->createForVersion(PhpVersion::getNewestSupported());
}





public function createForHostVersion(): Parser {
return $this->createForVersion(PhpVersion::getHostVersion());
}
}
