<?php

declare(strict_types=1);

namespace Dotenv\Parser;

final class Lexer
{



private const PATTERNS = [
'[\r\n]{1,1000}', '[^\S\r\n]{1,1000}', '\\\\', '\'', '"', '\\#', '\\$', '([^(\s\\\\\'"\\#\\$)]|\\(|\\)){1,1000}',
];








private function __construct()
{

}











public static function lex(string $content)
{
static $regex;

if ($regex === null) {
$regex = '(('.\implode(')|(', self::PATTERNS).'))A';
}

$offset = 0;

while (isset($content[$offset])) {
if (!\preg_match($regex, $content, $matches, 0, $offset)) {
throw new \Error(\sprintf('Lexer encountered unexpected character [%s].', $content[$offset]));
}

$offset += \strlen($matches[0]);

yield $matches[0];
}
}
}
