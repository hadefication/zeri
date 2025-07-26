<?php declare(strict_types=1);

namespace PhpParser;

require __DIR__ . '/compatibility_tokens.php';

class Lexer {
















public function tokenize(string $code, ?ErrorHandler $errorHandler = null): array {
if (null === $errorHandler) {
$errorHandler = new ErrorHandler\Throwing();
}

$scream = ini_set('xdebug.scream', '0');

$tokens = @Token::tokenize($code);
$this->postprocessTokens($tokens, $errorHandler);

if (false !== $scream) {
ini_set('xdebug.scream', $scream);
}

return $tokens;
}

private function handleInvalidCharacter(Token $token, ErrorHandler $errorHandler): void {
$chr = $token->text;
if ($chr === "\0") {

$errorMsg = 'Unexpected null byte';
} else {
$errorMsg = sprintf(
'Unexpected character "%s" (ASCII %d)', $chr, ord($chr)
);
}

$errorHandler->handleError(new Error($errorMsg, [
'startLine' => $token->line,
'endLine' => $token->line,
'startFilePos' => $token->pos,
'endFilePos' => $token->pos,
]));
}

private function isUnterminatedComment(Token $token): bool {
return $token->is([\T_COMMENT, \T_DOC_COMMENT])
&& substr($token->text, 0, 2) === '/*'
&& substr($token->text, -2) !== '*/';
}




protected function postprocessTokens(array &$tokens, ErrorHandler $errorHandler): void {






$numTokens = \count($tokens);
if ($numTokens === 0) {

$tokens[] = new Token(0, "\0", 1, 0);
return;
}

for ($i = 0; $i < $numTokens; $i++) {
$token = $tokens[$i];
if ($token->id === \T_BAD_CHARACTER) {
$this->handleInvalidCharacter($token, $errorHandler);
}

if ($token->id === \ord('&')) {
$next = $i + 1;
while (isset($tokens[$next]) && $tokens[$next]->id === \T_WHITESPACE) {
$next++;
}
$followedByVarOrVarArg = isset($tokens[$next]) &&
$tokens[$next]->is([\T_VARIABLE, \T_ELLIPSIS]);
$token->id = $followedByVarOrVarArg
? \T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG
: \T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG;
}
}


$lastToken = $tokens[$numTokens - 1];
if ($this->isUnterminatedComment($lastToken)) {
$errorHandler->handleError(new Error('Unterminated comment', [
'startLine' => $lastToken->line,
'endLine' => $lastToken->getEndLine(),
'startFilePos' => $lastToken->pos,
'endFilePos' => $lastToken->getEndPos(),
]));
}


$tokens[] = new Token(0, "\0", $lastToken->getEndLine(), $lastToken->getEndPos());
}
}
