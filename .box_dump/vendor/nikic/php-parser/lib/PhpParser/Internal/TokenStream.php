<?php declare(strict_types=1);

namespace PhpParser\Internal;

use PhpParser\Token;






class TokenStream {

private array $tokens;

private array $indentMap;






public function __construct(array $tokens, int $tabWidth) {
$this->tokens = $tokens;
$this->indentMap = $this->calcIndentMap($tabWidth);
}







public function haveParens(int $startPos, int $endPos): bool {
return $this->haveTokenImmediatelyBefore($startPos, '(')
&& $this->haveTokenImmediatelyAfter($endPos, ')');
}







public function haveBraces(int $startPos, int $endPos): bool {
return ($this->haveTokenImmediatelyBefore($startPos, '{')
|| $this->haveTokenImmediatelyBefore($startPos, T_CURLY_OPEN))
&& $this->haveTokenImmediatelyAfter($endPos, '}');
}











public function haveTokenImmediatelyBefore(int $pos, $expectedTokenType): bool {
$tokens = $this->tokens;
$pos--;
for (; $pos >= 0; $pos--) {
$token = $tokens[$pos];
if ($token->is($expectedTokenType)) {
return true;
}
if (!$token->isIgnorable()) {
break;
}
}
return false;
}











public function haveTokenImmediatelyAfter(int $pos, $expectedTokenType): bool {
$tokens = $this->tokens;
$pos++;
for ($c = \count($tokens); $pos < $c; $pos++) {
$token = $tokens[$pos];
if ($token->is($expectedTokenType)) {
return true;
}
if (!$token->isIgnorable()) {
break;
}
}
return false;
}


public function skipLeft(int $pos, $skipTokenType): int {
$tokens = $this->tokens;

$pos = $this->skipLeftWhitespace($pos);
if ($skipTokenType === \T_WHITESPACE) {
return $pos;
}

if (!$tokens[$pos]->is($skipTokenType)) {

throw new \Exception('Encountered unexpected token');
}
$pos--;

return $this->skipLeftWhitespace($pos);
}


public function skipRight(int $pos, $skipTokenType): int {
$tokens = $this->tokens;

$pos = $this->skipRightWhitespace($pos);
if ($skipTokenType === \T_WHITESPACE) {
return $pos;
}

if (!$tokens[$pos]->is($skipTokenType)) {

throw new \Exception('Encountered unexpected token');
}
$pos++;

return $this->skipRightWhitespace($pos);
}







public function skipLeftWhitespace(int $pos): int {
$tokens = $this->tokens;
for (; $pos >= 0; $pos--) {
if (!$tokens[$pos]->isIgnorable()) {
break;
}
}
return $pos;
}







public function skipRightWhitespace(int $pos): int {
$tokens = $this->tokens;
for ($count = \count($tokens); $pos < $count; $pos++) {
if (!$tokens[$pos]->isIgnorable()) {
break;
}
}
return $pos;
}


public function findRight(int $pos, $findTokenType): int {
$tokens = $this->tokens;
for ($count = \count($tokens); $pos < $count; $pos++) {
if ($tokens[$pos]->is($findTokenType)) {
return $pos;
}
}
return -1;
}









public function haveTokenInRange(int $startPos, int $endPos, $tokenType): bool {
$tokens = $this->tokens;
for ($pos = $startPos; $pos < $endPos; $pos++) {
if ($tokens[$pos]->is($tokenType)) {
return true;
}
}
return false;
}

public function haveTagInRange(int $startPos, int $endPos): bool {
return $this->haveTokenInRange($startPos, $endPos, \T_OPEN_TAG)
|| $this->haveTokenInRange($startPos, $endPos, \T_CLOSE_TAG);
}








public function getIndentationBefore(int $pos): int {
return $this->indentMap[$pos];
}










public function getTokenCode(int $from, int $to, int $indent): string {
$tokens = $this->tokens;
$result = '';
for ($pos = $from; $pos < $to; $pos++) {
$token = $tokens[$pos];
$id = $token->id;
$text = $token->text;
if ($id === \T_CONSTANT_ENCAPSED_STRING || $id === \T_ENCAPSED_AND_WHITESPACE) {
$result .= $text;
} else {

if ($indent < 0) {
$result .= str_replace("\n" . str_repeat(" ", -$indent), "\n", $text);
} elseif ($indent > 0) {
$result .= str_replace("\n", "\n" . str_repeat(" ", $indent), $text);
} else {
$result .= $text;
}
}
}
return $result;
}






private function calcIndentMap(int $tabWidth): array {
$indentMap = [];
$indent = 0;
foreach ($this->tokens as $i => $token) {
$indentMap[] = $indent;

if ($token->id === \T_WHITESPACE) {
$content = $token->text;
$newlinePos = \strrpos($content, "\n");
if (false !== $newlinePos) {
$indent = $this->getIndent(\substr($content, $newlinePos + 1), $tabWidth);
} elseif ($i === 1 && $this->tokens[0]->id === \T_OPEN_TAG &&
$this->tokens[0]->text[\strlen($this->tokens[0]->text) - 1] === "\n") {

$indent = $this->getIndent($content, $tabWidth);
}
}
}


$indentMap[] = $indent;

return $indentMap;
}

private function getIndent(string $ws, int $tabWidth): int {
$spaces = \substr_count($ws, " ");
$tabs = \substr_count($ws, "\t");
assert(\strlen($ws) === $spaces + $tabs);
return $spaces + $tabs * $tabWidth;
}
}
