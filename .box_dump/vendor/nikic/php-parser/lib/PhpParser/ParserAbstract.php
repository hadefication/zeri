<?php declare(strict_types=1);

namespace PhpParser;






use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Identifier;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\UseItem;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeVisitor\CommentAnnotatingVisitor;

abstract class ParserAbstract implements Parser {
private const SYMBOL_NONE = -1;


protected Lexer $lexer;

protected PhpVersion $phpVersion;






protected int $tokenToSymbolMapSize;

protected int $actionTableSize;

protected int $gotoTableSize;


protected int $invalidSymbol;

protected int $errorSymbol;

protected int $defaultAction;

protected int $unexpectedTokenRule;

protected int $YY2TBLSTATE;

protected int $numNonLeafStates;


protected array $phpTokenToSymbol;

protected array $dropTokens;

protected array $tokenToSymbol;

protected array $symbolToName;

protected array $productions;




protected array $actionBase;

protected array $action;


protected array $actionCheck;

protected array $actionDefault;

protected array $reduceCallbacks;



protected array $gotoBase;

protected array $goto;


protected array $gotoCheck;

protected array $gotoDefault;



protected array $ruleToNonTerminal;


protected array $ruleToLength;






protected $semValue;

protected array $semStack;

protected array $tokenStartStack;

protected array $tokenEndStack;


protected ErrorHandler $errorHandler;

protected int $errorState;


protected ?\SplObjectStorage $createdArrays;


protected array $tokens;

protected int $tokenPos;




abstract protected function initReduceCallbacks(): void;














public function __construct(Lexer $lexer, ?PhpVersion $phpVersion = null) {
$this->lexer = $lexer;
$this->phpVersion = $phpVersion ?? PhpVersion::getNewestSupported();

$this->initReduceCallbacks();
$this->phpTokenToSymbol = $this->createTokenMap();
$this->dropTokens = array_fill_keys(
[\T_WHITESPACE, \T_OPEN_TAG, \T_COMMENT, \T_DOC_COMMENT, \T_BAD_CHARACTER], true
);
}














public function parse(string $code, ?ErrorHandler $errorHandler = null): ?array {
$this->errorHandler = $errorHandler ?: new ErrorHandler\Throwing();
$this->createdArrays = new \SplObjectStorage();

$this->tokens = $this->lexer->tokenize($code, $this->errorHandler);
$result = $this->doParse();




foreach ($this->createdArrays as $node) {
foreach ($node->items as $item) {
if ($item->value instanceof Expr\Error) {
$this->errorHandler->handleError(
new Error('Cannot use empty array elements in arrays', $item->getAttributes()));
}
}
}



$this->tokenStartStack = [];
$this->tokenEndStack = [];
$this->semStack = [];
$this->semValue = null;
$this->createdArrays = null;

if ($result !== null) {
$traverser = new NodeTraverser(new CommentAnnotatingVisitor($this->tokens));
$traverser->traverse($result);
}

return $result;
}

public function getTokens(): array {
return $this->tokens;
}


protected function doParse(): ?array {

$symbol = self::SYMBOL_NONE;
$tokenValue = null;
$this->tokenPos = -1;


$this->tokenStartStack = [];
$this->tokenEndStack = [0];


$state = 0;
$stateStack = [$state];


$this->semStack = [];


$stackPos = 0;

$this->errorState = 0;

for (;;) {


if ($this->actionBase[$state] === 0) {
$rule = $this->actionDefault[$state];
} else {
if ($symbol === self::SYMBOL_NONE) {
do {
$token = $this->tokens[++$this->tokenPos];
$tokenId = $token->id;
} while (isset($this->dropTokens[$tokenId]));


$tokenValue = $token->text;
if (!isset($this->phpTokenToSymbol[$tokenId])) {
throw new \RangeException(sprintf(
'The lexer returned an invalid token (id=%d, value=%s)',
$tokenId, $tokenValue
));
}
$symbol = $this->phpTokenToSymbol[$tokenId];


}

$idx = $this->actionBase[$state] + $symbol;
if ((($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol)
|| ($state < $this->YY2TBLSTATE
&& ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) >= 0
&& $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol))
&& ($action = $this->action[$idx]) !== $this->defaultAction) {







if ($action > 0) {



++$stackPos;
$stateStack[$stackPos] = $state = $action;
$this->semStack[$stackPos] = $tokenValue;
$this->tokenStartStack[$stackPos] = $this->tokenPos;
$this->tokenEndStack[$stackPos] = $this->tokenPos;
$symbol = self::SYMBOL_NONE;

if ($this->errorState) {
--$this->errorState;
}

if ($action < $this->numNonLeafStates) {
continue;
}


$rule = $action - $this->numNonLeafStates;
} else {
$rule = -$action;
}
} else {
$rule = $this->actionDefault[$state];
}
}

for (;;) {
if ($rule === 0) {


return $this->semValue;
}
if ($rule !== $this->unexpectedTokenRule) {



$ruleLength = $this->ruleToLength[$rule];
try {
$callback = $this->reduceCallbacks[$rule];
if ($callback !== null) {
$callback($this, $stackPos);
} elseif ($ruleLength > 0) {
$this->semValue = $this->semStack[$stackPos - $ruleLength + 1];
}
} catch (Error $e) {
if (-1 === $e->getStartLine()) {
$e->setStartLine($this->tokens[$this->tokenPos]->line);
}

$this->emitError($e);

return null;
}


$lastTokenEnd = $this->tokenEndStack[$stackPos];
$stackPos -= $ruleLength;
$nonTerminal = $this->ruleToNonTerminal[$rule];
$idx = $this->gotoBase[$nonTerminal] + $stateStack[$stackPos];
if ($idx >= 0 && $idx < $this->gotoTableSize && $this->gotoCheck[$idx] === $nonTerminal) {
$state = $this->goto[$idx];
} else {
$state = $this->gotoDefault[$nonTerminal];
}

++$stackPos;
$stateStack[$stackPos] = $state;
$this->semStack[$stackPos] = $this->semValue;
$this->tokenEndStack[$stackPos] = $lastTokenEnd;
if ($ruleLength === 0) {

$this->tokenStartStack[$stackPos] = $this->tokenPos;
}
} else {

switch ($this->errorState) {
case 0:
$msg = $this->getErrorMessage($symbol, $state);
$this->emitError(new Error($msg, $this->getAttributesForToken($this->tokenPos)));


case 1:
case 2:
$this->errorState = 3;


while (!(
(($idx = $this->actionBase[$state] + $this->errorSymbol) >= 0
&& $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
|| ($state < $this->YY2TBLSTATE
&& ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $this->errorSymbol) >= 0
&& $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
) || ($action = $this->action[$idx]) === $this->defaultAction) { 
if ($stackPos <= 0) {

return null;
}
$state = $stateStack[--$stackPos];

}


++$stackPos;
$stateStack[$stackPos] = $state = $action;



$this->tokenStartStack[$stackPos] = $this->tokenPos;
$this->tokenEndStack[$stackPos] = $this->tokenEndStack[$stackPos - 1];
break;

case 3:
if ($symbol === 0) {

return null;
}


$symbol = self::SYMBOL_NONE;
break 2;
}
}

if ($state < $this->numNonLeafStates) {
break;
}


$rule = $state - $this->numNonLeafStates;
}
}
}

protected function emitError(Error $error): void {
$this->errorHandler->handleError($error);
}









protected function getErrorMessage(int $symbol, int $state): string {
$expectedString = '';
if ($expected = $this->getExpectedTokens($state)) {
$expectedString = ', expecting ' . implode(' or ', $expected);
}

return 'Syntax error, unexpected ' . $this->symbolToName[$symbol] . $expectedString;
}








protected function getExpectedTokens(int $state): array {
$expected = [];

$base = $this->actionBase[$state];
foreach ($this->symbolToName as $symbol => $name) {
$idx = $base + $symbol;
if ($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
|| $state < $this->YY2TBLSTATE
&& ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) >= 0
&& $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
) {
if ($this->action[$idx] !== $this->unexpectedTokenRule
&& $this->action[$idx] !== $this->defaultAction
&& $symbol !== $this->errorSymbol
) {
if (count($expected) === 4) {

return [];
}

$expected[] = $name;
}
}
}

return $expected;
}








protected function getAttributes(int $tokenStartPos, int $tokenEndPos): array {
$startToken = $this->tokens[$tokenStartPos];
$afterEndToken = $this->tokens[$tokenEndPos + 1];
return [
'startLine' => $startToken->line,
'startTokenPos' => $tokenStartPos,
'startFilePos' => $startToken->pos,
'endLine' => $afterEndToken->line,
'endTokenPos' => $tokenEndPos,
'endFilePos' => $afterEndToken->pos - 1,
];
}






protected function getAttributesForToken(int $tokenPos): array {
if ($tokenPos < \count($this->tokens) - 1) {
return $this->getAttributes($tokenPos, $tokenPos);
}


$token = $this->tokens[$tokenPos];
return [
'startLine' => $token->line,
'startTokenPos' => $tokenPos,
'startFilePos' => $token->pos,
'endLine' => $token->line,
'endTokenPos' => $tokenPos,
'endFilePos' => $token->pos,
];
}














































protected function handleNamespaces(array $stmts): array {
$hasErrored = false;
$style = $this->getNamespacingStyle($stmts);
if (null === $style) {

return $stmts;
}
if ('brace' === $style) {

$afterFirstNamespace = false;
foreach ($stmts as $stmt) {
if ($stmt instanceof Node\Stmt\Namespace_) {
$afterFirstNamespace = true;
} elseif (!$stmt instanceof Node\Stmt\HaltCompiler
&& !$stmt instanceof Node\Stmt\Nop
&& $afterFirstNamespace && !$hasErrored) {
$this->emitError(new Error(
'No code may exist outside of namespace {}', $stmt->getAttributes()));
$hasErrored = true; 
}
}
return $stmts;
} else {

$resultStmts = [];
$targetStmts = &$resultStmts;
$lastNs = null;
foreach ($stmts as $stmt) {
if ($stmt instanceof Node\Stmt\Namespace_) {
if ($lastNs !== null) {
$this->fixupNamespaceAttributes($lastNs);
}
if ($stmt->stmts === null) {
$stmt->stmts = [];
$targetStmts = &$stmt->stmts;
$resultStmts[] = $stmt;
} else {

$resultStmts[] = $stmt;
$targetStmts = &$resultStmts;
}
$lastNs = $stmt;
} elseif ($stmt instanceof Node\Stmt\HaltCompiler) {

$resultStmts[] = $stmt;
} else {
$targetStmts[] = $stmt;
}
}
if ($lastNs !== null) {
$this->fixupNamespaceAttributes($lastNs);
}
return $resultStmts;
}
}

private function fixupNamespaceAttributes(Node\Stmt\Namespace_ $stmt): void {


if (empty($stmt->stmts)) {
return;
}



$endAttributes = ['endLine', 'endFilePos', 'endTokenPos'];
$lastStmt = $stmt->stmts[count($stmt->stmts) - 1];
foreach ($endAttributes as $endAttribute) {
if ($lastStmt->hasAttribute($endAttribute)) {
$stmt->setAttribute($endAttribute, $lastStmt->getAttribute($endAttribute));
}
}
}


private function getNamespaceErrorAttributes(Namespace_ $node): array {
$attrs = $node->getAttributes();

if (isset($attrs['startLine'])) {
$attrs['endLine'] = $attrs['startLine'];
}
if (isset($attrs['startTokenPos'])) {
$attrs['endTokenPos'] = $attrs['startTokenPos'];
}
if (isset($attrs['startFilePos'])) {
$attrs['endFilePos'] = $attrs['startFilePos'] + \strlen('namespace') - 1;
}
return $attrs;
}








private function getNamespacingStyle(array $stmts): ?string {
$style = null;
$hasNotAllowedStmts = false;
foreach ($stmts as $i => $stmt) {
if ($stmt instanceof Node\Stmt\Namespace_) {
$currentStyle = null === $stmt->stmts ? 'semicolon' : 'brace';
if (null === $style) {
$style = $currentStyle;
if ($hasNotAllowedStmts) {
$this->emitError(new Error(
'Namespace declaration statement has to be the very first statement in the script',
$this->getNamespaceErrorAttributes($stmt)
));
}
} elseif ($style !== $currentStyle) {
$this->emitError(new Error(
'Cannot mix bracketed namespace declarations with unbracketed namespace declarations',
$this->getNamespaceErrorAttributes($stmt)
));

return 'semicolon';
}
continue;
}


if ($stmt instanceof Node\Stmt\Declare_
|| $stmt instanceof Node\Stmt\HaltCompiler
|| $stmt instanceof Node\Stmt\Nop) {
continue;
}


if ($i === 0 && $stmt instanceof Node\Stmt\InlineHTML && preg_match('/\A#!.*\r?\n\z/', $stmt->value)) {
continue;
}


$hasNotAllowedStmts = true;
}
return $style;
}


protected function handleBuiltinTypes(Name $name) {
if (!$name->isUnqualified()) {
return $name;
}

$lowerName = $name->toLowerString();
if (!$this->phpVersion->supportsBuiltinType($lowerName)) {
return $name;
}

return new Node\Identifier($lowerName, $name->getAttributes());
}








protected function getAttributesAt(int $stackPos): array {
return $this->getAttributes($this->tokenStartStack[$stackPos], $this->tokenEndStack[$stackPos]);
}

protected function getFloatCastKind(string $cast): int {
$cast = strtolower($cast);
if (strpos($cast, 'float') !== false) {
return Double::KIND_FLOAT;
}

if (strpos($cast, 'real') !== false) {
return Double::KIND_REAL;
}

return Double::KIND_DOUBLE;
}


protected function parseLNumber(string $str, array $attributes, bool $allowInvalidOctal = false): Int_ {
try {
return Int_::fromString($str, $attributes, $allowInvalidOctal);
} catch (Error $error) {
$this->emitError($error);

return new Int_(0, $attributes);
}
}









protected function parseNumString(string $str, array $attributes) {
if (!preg_match('/^(?:0|-?[1-9][0-9]*)$/', $str)) {
return new String_($str, $attributes);
}

$num = +$str;
if (!is_int($num)) {
return new String_($str, $attributes);
}

return new Int_($num, $attributes);
}


protected function stripIndentation(
string $string, int $indentLen, string $indentChar,
bool $newlineAtStart, bool $newlineAtEnd, array $attributes
): string {
if ($indentLen === 0) {
return $string;
}

$start = $newlineAtStart ? '(?:(?<=\n)|\A)' : '(?<=\n)';
$end = $newlineAtEnd ? '(?:(?=[\r\n])|\z)' : '(?=[\r\n])';
$regex = '/' . $start . '([ \t]*)(' . $end . ')?/';
return preg_replace_callback(
$regex,
function ($matches) use ($indentLen, $indentChar, $attributes) {
$prefix = substr($matches[1], 0, $indentLen);
if (false !== strpos($prefix, $indentChar === " " ? "\t" : " ")) {
$this->emitError(new Error(
'Invalid indentation - tabs and spaces cannot be mixed', $attributes
));
} elseif (strlen($prefix) < $indentLen && !isset($matches[2])) {
$this->emitError(new Error(
'Invalid body indentation level ' .
'(expecting an indentation level of at least ' . $indentLen . ')',
$attributes
));
}
return substr($matches[0], strlen($prefix));
},
$string
);
}






protected function parseDocString(
string $startToken, $contents, string $endToken,
array $attributes, array $endTokenAttributes, bool $parseUnicodeEscape
): Expr {
$kind = strpos($startToken, "'") === false
? String_::KIND_HEREDOC : String_::KIND_NOWDOC;

$regex = '/\A[bB]?<<<[ \t]*[\'"]?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[\'"]?(?:\r\n|\n|\r)\z/';
$result = preg_match($regex, $startToken, $matches);
assert($result === 1);
$label = $matches[1];

$result = preg_match('/\A[ \t]*/', $endToken, $matches);
assert($result === 1);
$indentation = $matches[0];

$attributes['kind'] = $kind;
$attributes['docLabel'] = $label;
$attributes['docIndentation'] = $indentation;

$indentHasSpaces = false !== strpos($indentation, " ");
$indentHasTabs = false !== strpos($indentation, "\t");
if ($indentHasSpaces && $indentHasTabs) {
$this->emitError(new Error(
'Invalid indentation - tabs and spaces cannot be mixed',
$endTokenAttributes
));


$indentation = '';
}

$indentLen = \strlen($indentation);
$indentChar = $indentHasSpaces ? " " : "\t";

if (\is_string($contents)) {
if ($contents === '') {
$attributes['rawValue'] = $contents;
return new String_('', $attributes);
}

$contents = $this->stripIndentation(
$contents, $indentLen, $indentChar, true, true, $attributes
);
$contents = preg_replace('~(\r\n|\n|\r)\z~', '', $contents);
$attributes['rawValue'] = $contents;

if ($kind === String_::KIND_HEREDOC) {
$contents = String_::parseEscapeSequences($contents, null, $parseUnicodeEscape);
}

return new String_($contents, $attributes);
} else {
assert(count($contents) > 0);
if (!$contents[0] instanceof Node\InterpolatedStringPart) {

$this->stripIndentation(
'', $indentLen, $indentChar, true, false, $contents[0]->getAttributes()
);
}

$newContents = [];
foreach ($contents as $i => $part) {
if ($part instanceof Node\InterpolatedStringPart) {
$isLast = $i === \count($contents) - 1;
$part->value = $this->stripIndentation(
$part->value, $indentLen, $indentChar,
$i === 0, $isLast, $part->getAttributes()
);
if ($isLast) {
$part->value = preg_replace('~(\r\n|\n|\r)\z~', '', $part->value);
}
$part->setAttribute('rawValue', $part->value);
$part->value = String_::parseEscapeSequences($part->value, null, $parseUnicodeEscape);
if ('' === $part->value) {
continue;
}
}
$newContents[] = $part;
}
return new InterpolatedString($newContents, $attributes);
}
}

protected function createCommentFromToken(Token $token, int $tokenPos): Comment {
assert($token->id === \T_COMMENT || $token->id == \T_DOC_COMMENT);
return \T_DOC_COMMENT === $token->id
? new Comment\Doc($token->text, $token->line, $token->pos, $tokenPos,
$token->getEndLine(), $token->getEndPos() - 1, $tokenPos)
: new Comment($token->text, $token->line, $token->pos, $tokenPos,
$token->getEndLine(), $token->getEndPos() - 1, $tokenPos);
}




protected function getCommentBeforeToken(int $tokenPos): ?Comment {
while (--$tokenPos >= 0) {
$token = $this->tokens[$tokenPos];
if (!isset($this->dropTokens[$token->id])) {
break;
}

if ($token->id === \T_COMMENT || $token->id === \T_DOC_COMMENT) {
return $this->createCommentFromToken($token, $tokenPos);
}
}
return null;
}




protected function maybeCreateZeroLengthNop(int $tokenPos): ?Nop {
$comment = $this->getCommentBeforeToken($tokenPos);
if ($comment === null) {
return null;
}

$commentEndLine = $comment->getEndLine();
$commentEndFilePos = $comment->getEndFilePos();
$commentEndTokenPos = $comment->getEndTokenPos();
$attributes = [
'startLine' => $commentEndLine,
'endLine' => $commentEndLine,
'startFilePos' => $commentEndFilePos + 1,
'endFilePos' => $commentEndFilePos,
'startTokenPos' => $commentEndTokenPos + 1,
'endTokenPos' => $commentEndTokenPos,
];
return new Nop($attributes);
}

protected function maybeCreateNop(int $tokenStartPos, int $tokenEndPos): ?Nop {
if ($this->getCommentBeforeToken($tokenStartPos) === null) {
return null;
}
return new Nop($this->getAttributes($tokenStartPos, $tokenEndPos));
}

protected function handleHaltCompiler(): string {

$nextToken = $this->tokens[$this->tokenPos + 1];
$this->tokenPos = \count($this->tokens) - 2;


return $nextToken->id === \T_INLINE_HTML ? $nextToken->text : '';
}

protected function inlineHtmlHasLeadingNewline(int $stackPos): bool {
$tokenPos = $this->tokenStartStack[$stackPos];
$token = $this->tokens[$tokenPos];
assert($token->id == \T_INLINE_HTML);
if ($tokenPos > 0) {
$prevToken = $this->tokens[$tokenPos - 1];
assert($prevToken->id == \T_CLOSE_TAG);
return false !== strpos($prevToken->text, "\n")
|| false !== strpos($prevToken->text, "\r");
}
return true;
}




protected function createEmptyElemAttributes(int $tokenPos): array {
return $this->getAttributesForToken($tokenPos);
}

protected function fixupArrayDestructuring(Array_ $node): Expr\List_ {
$this->createdArrays->detach($node);
return new Expr\List_(array_map(function (Node\ArrayItem $item) {
if ($item->value instanceof Expr\Error) {

return null;
}
if ($item->value instanceof Array_) {
return new Node\ArrayItem(
$this->fixupArrayDestructuring($item->value),
$item->key, $item->byRef, $item->getAttributes());
}
return $item;
}, $node->items), ['kind' => Expr\List_::KIND_ARRAY] + $node->getAttributes());
}

protected function postprocessList(Expr\List_ $node): void {
foreach ($node->items as $i => $item) {
if ($item->value instanceof Expr\Error) {

$node->items[$i] = null;
}
}
}


protected function fixupAlternativeElse($node): void {

$numStmts = \count($node->stmts);
if ($numStmts !== 0 && $node->stmts[$numStmts - 1] instanceof Nop) {
$nopAttrs = $node->stmts[$numStmts - 1]->getAttributes();
if (isset($nopAttrs['endLine'])) {
$node->setAttribute('endLine', $nopAttrs['endLine']);
}
if (isset($nopAttrs['endFilePos'])) {
$node->setAttribute('endFilePos', $nopAttrs['endFilePos']);
}
if (isset($nopAttrs['endTokenPos'])) {
$node->setAttribute('endTokenPos', $nopAttrs['endTokenPos']);
}
}
}

protected function checkClassModifier(int $a, int $b, int $modifierPos): void {
try {
Modifiers::verifyClassModifier($a, $b);
} catch (Error $error) {
$error->setAttributes($this->getAttributesAt($modifierPos));
$this->emitError($error);
}
}

protected function checkModifier(int $a, int $b, int $modifierPos): void {

try {
Modifiers::verifyModifier($a, $b);
} catch (Error $error) {
$error->setAttributes($this->getAttributesAt($modifierPos));
$this->emitError($error);
}
}

protected function checkParam(Param $node): void {
if ($node->variadic && null !== $node->default) {
$this->emitError(new Error(
'Variadic parameter cannot have a default value',
$node->default->getAttributes()
));
}
}

protected function checkTryCatch(TryCatch $node): void {
if (empty($node->catches) && null === $node->finally) {
$this->emitError(new Error(
'Cannot use try without catch or finally', $node->getAttributes()
));
}
}

protected function checkNamespace(Namespace_ $node): void {
if (null !== $node->stmts) {
foreach ($node->stmts as $stmt) {
if ($stmt instanceof Namespace_) {
$this->emitError(new Error(
'Namespace declarations cannot be nested', $stmt->getAttributes()
));
}
}
}
}

private function checkClassName(?Identifier $name, int $namePos): void {
if (null !== $name && $name->isSpecialClassName()) {
$this->emitError(new Error(
sprintf('Cannot use \'%s\' as class name as it is reserved', $name),
$this->getAttributesAt($namePos)
));
}
}


private function checkImplementedInterfaces(array $interfaces): void {
foreach ($interfaces as $interface) {
if ($interface->isSpecialClassName()) {
$this->emitError(new Error(
sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface),
$interface->getAttributes()
));
}
}
}

protected function checkClass(Class_ $node, int $namePos): void {
$this->checkClassName($node->name, $namePos);

if ($node->extends && $node->extends->isSpecialClassName()) {
$this->emitError(new Error(
sprintf('Cannot use \'%s\' as class name as it is reserved', $node->extends),
$node->extends->getAttributes()
));
}

$this->checkImplementedInterfaces($node->implements);
}

protected function checkInterface(Interface_ $node, int $namePos): void {
$this->checkClassName($node->name, $namePos);
$this->checkImplementedInterfaces($node->extends);
}

protected function checkEnum(Enum_ $node, int $namePos): void {
$this->checkClassName($node->name, $namePos);
$this->checkImplementedInterfaces($node->implements);
}

protected function checkClassMethod(ClassMethod $node, int $modifierPos): void {
if ($node->flags & Modifiers::STATIC) {
switch ($node->name->toLowerString()) {
case '__construct':
$this->emitError(new Error(
sprintf('Constructor %s() cannot be static', $node->name),
$this->getAttributesAt($modifierPos)));
break;
case '__destruct':
$this->emitError(new Error(
sprintf('Destructor %s() cannot be static', $node->name),
$this->getAttributesAt($modifierPos)));
break;
case '__clone':
$this->emitError(new Error(
sprintf('Clone method %s() cannot be static', $node->name),
$this->getAttributesAt($modifierPos)));
break;
}
}

if ($node->flags & Modifiers::READONLY) {
$this->emitError(new Error(
sprintf('Method %s() cannot be readonly', $node->name),
$this->getAttributesAt($modifierPos)));
}
}

protected function checkClassConst(ClassConst $node, int $modifierPos): void {
foreach ([Modifiers::STATIC, Modifiers::ABSTRACT, Modifiers::READONLY] as $modifier) {
if ($node->flags & $modifier) {
$this->emitError(new Error(
"Cannot use '" . Modifiers::toString($modifier) . "' as constant modifier",
$this->getAttributesAt($modifierPos)));
}
}
}

protected function checkUseUse(UseItem $node, int $namePos): void {
if ($node->alias && $node->alias->isSpecialClassName()) {
$this->emitError(new Error(
sprintf(
'Cannot use %s as %s because \'%2$s\' is a special class name',
$node->name, $node->alias
),
$this->getAttributesAt($namePos)
));
}
}

protected function checkPropertyHooksForMultiProperty(Property $property, int $hookPos): void {
if (count($property->props) > 1) {
$this->emitError(new Error(
'Cannot use hooks when declaring multiple properties', $this->getAttributesAt($hookPos)));
}
}


protected function checkEmptyPropertyHookList(array $hooks, int $hookPos): void {
if (empty($hooks)) {
$this->emitError(new Error(
'Property hook list cannot be empty', $this->getAttributesAt($hookPos)));
}
}

protected function checkPropertyHook(PropertyHook $hook, ?int $paramListPos): void {
$name = $hook->name->toLowerString();
if ($name !== 'get' && $name !== 'set') {
$this->emitError(new Error(
'Unknown hook "' . $hook->name . '", expected "get" or "set"',
$hook->name->getAttributes()));
}
if ($name === 'get' && $paramListPos !== null) {
$this->emitError(new Error(
'get hook must not have a parameter list', $this->getAttributesAt($paramListPos)));
}
}

protected function checkPropertyHookModifiers(int $a, int $b, int $modifierPos): void {
try {
Modifiers::verifyModifier($a, $b);
} catch (Error $error) {
$error->setAttributes($this->getAttributesAt($modifierPos));
$this->emitError($error);
}

if ($b != Modifiers::FINAL) {
$this->emitError(new Error(
'Cannot use the ' . Modifiers::toString($b) . ' modifier on a property hook',
$this->getAttributesAt($modifierPos)));
}
}

protected function checkConstantAttributes(Const_ $node): void {
if ($node->attrGroups !== [] && count($node->consts) > 1) {
$this->emitError(new Error(
'Cannot use attributes on multiple constants at once', $node->getAttributes()));
}
}




protected function addPropertyNameToHooks(Node $node): void {
if ($node instanceof Property) {
$name = $node->props[0]->name->toString();
} else {
$name = $node->var->name;
}
foreach ($node->hooks as $hook) {
$hook->setAttribute('propertyName', $name);
}
}


private function isSimpleExit(array $args): bool {
if (\count($args) === 0) {
return true;
}
if (\count($args) === 1) {
$arg = $args[0];
return $arg instanceof Arg && $arg->name === null &&
$arg->byRef === false && $arg->unpack === false;
}
return false;
}





protected function createExitExpr(string $name, int $namePos, array $args, array $attrs): Expr {
if ($this->isSimpleExit($args)) {

$attrs['kind'] = strtolower($name) === 'exit' ? Expr\Exit_::KIND_EXIT : Expr\Exit_::KIND_DIE;
return new Expr\Exit_(\count($args) === 1 ? $args[0]->value : null, $attrs);
}
return new Expr\FuncCall(new Name($name, $this->getAttributesAt($namePos)), $args, $attrs);
}










protected function createTokenMap(): array {
$tokenMap = [];


for ($i = 0; $i < 256; ++$i) {
$tokenMap[$i] = $i;
}

foreach ($this->symbolToName as $name) {
if ($name[0] === 'T') {
$tokenMap[\constant($name)] = constant(static::class . '::' . $name);
}
}


$tokenMap[\T_OPEN_TAG_WITH_ECHO] = static::T_ECHO;

$tokenMap[\T_CLOSE_TAG] = ord(';');



$fullTokenMap = [];
foreach ($tokenMap as $phpToken => $extSymbol) {
$intSymbol = $this->tokenToSymbol[$extSymbol];
if ($intSymbol === $this->invalidSymbol) {
continue;
}
$fullTokenMap[$phpToken] = $intSymbol;
}

return $fullTokenMap;
}
}
