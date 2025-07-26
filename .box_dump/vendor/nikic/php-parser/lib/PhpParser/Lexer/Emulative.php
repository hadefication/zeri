<?php declare(strict_types=1);

namespace PhpParser\Lexer;

use PhpParser\Error;
use PhpParser\ErrorHandler;
use PhpParser\Lexer;
use PhpParser\Lexer\TokenEmulator\AsymmetricVisibilityTokenEmulator;
use PhpParser\Lexer\TokenEmulator\AttributeEmulator;
use PhpParser\Lexer\TokenEmulator\EnumTokenEmulator;
use PhpParser\Lexer\TokenEmulator\ExplicitOctalEmulator;
use PhpParser\Lexer\TokenEmulator\MatchTokenEmulator;
use PhpParser\Lexer\TokenEmulator\NullsafeTokenEmulator;
use PhpParser\Lexer\TokenEmulator\PropertyTokenEmulator;
use PhpParser\Lexer\TokenEmulator\ReadonlyFunctionTokenEmulator;
use PhpParser\Lexer\TokenEmulator\ReadonlyTokenEmulator;
use PhpParser\Lexer\TokenEmulator\ReverseEmulator;
use PhpParser\Lexer\TokenEmulator\TokenEmulator;
use PhpParser\PhpVersion;
use PhpParser\Token;

class Emulative extends Lexer {

private array $patches = [];


private array $emulators = [];

private PhpVersion $targetPhpVersion;

private PhpVersion $hostPhpVersion;




public function __construct(?PhpVersion $phpVersion = null) {
$this->targetPhpVersion = $phpVersion ?? PhpVersion::getNewestSupported();
$this->hostPhpVersion = PhpVersion::getHostVersion();

$emulators = [
new MatchTokenEmulator(),
new NullsafeTokenEmulator(),
new AttributeEmulator(),
new EnumTokenEmulator(),
new ReadonlyTokenEmulator(),
new ExplicitOctalEmulator(),
new ReadonlyFunctionTokenEmulator(),
new PropertyTokenEmulator(),
new AsymmetricVisibilityTokenEmulator(),
];



foreach ($emulators as $emulator) {
$emulatorPhpVersion = $emulator->getPhpVersion();
if ($this->isForwardEmulationNeeded($emulatorPhpVersion)) {
$this->emulators[] = $emulator;
} elseif ($this->isReverseEmulationNeeded($emulatorPhpVersion)) {
$this->emulators[] = new ReverseEmulator($emulator);
}
}
}

public function tokenize(string $code, ?ErrorHandler $errorHandler = null): array {
$emulators = array_filter($this->emulators, function ($emulator) use ($code) {
return $emulator->isEmulationNeeded($code);
});

if (empty($emulators)) {

return parent::tokenize($code, $errorHandler);
}

if ($errorHandler === null) {
$errorHandler = new ErrorHandler\Throwing();
}

$this->patches = [];
foreach ($emulators as $emulator) {
$code = $emulator->preprocessCode($code, $this->patches);
}

$collector = new ErrorHandler\Collecting();
$tokens = parent::tokenize($code, $collector);
$this->sortPatches();
$tokens = $this->fixupTokens($tokens);

$errors = $collector->getErrors();
if (!empty($errors)) {
$this->fixupErrors($errors);
foreach ($errors as $error) {
$errorHandler->handleError($error);
}
}

foreach ($emulators as $emulator) {
$tokens = $emulator->emulate($code, $tokens);
}

return $tokens;
}

private function isForwardEmulationNeeded(PhpVersion $emulatorPhpVersion): bool {
return $this->hostPhpVersion->older($emulatorPhpVersion)
&& $this->targetPhpVersion->newerOrEqual($emulatorPhpVersion);
}

private function isReverseEmulationNeeded(PhpVersion $emulatorPhpVersion): bool {
return $this->hostPhpVersion->newerOrEqual($emulatorPhpVersion)
&& $this->targetPhpVersion->older($emulatorPhpVersion);
}

private function sortPatches(): void {


usort($this->patches, function ($p1, $p2) {
return $p1[0] <=> $p2[0];
});
}





private function fixupTokens(array $tokens): array {
if (\count($this->patches) === 0) {
return $tokens;
}


$patchIdx = 0;
list($patchPos, $patchType, $patchText) = $this->patches[$patchIdx];


$posDelta = 0;
$lineDelta = 0;
for ($i = 0, $c = \count($tokens); $i < $c; $i++) {
$token = $tokens[$i];
$pos = $token->pos;
$token->pos += $posDelta;
$token->line += $lineDelta;
$localPosDelta = 0;
$len = \strlen($token->text);
while ($patchPos >= $pos && $patchPos < $pos + $len) {
$patchTextLen = \strlen($patchText);
if ($patchType === 'remove') {
if ($patchPos === $pos && $patchTextLen === $len) {

array_splice($tokens, $i, 1, []);
$i--;
$c--;
} else {

$token->text = substr_replace(
$token->text, '', $patchPos - $pos + $localPosDelta, $patchTextLen
);
$localPosDelta -= $patchTextLen;
}
$lineDelta -= \substr_count($patchText, "\n");
} elseif ($patchType === 'add') {

$token->text = substr_replace(
$token->text, $patchText, $patchPos - $pos + $localPosDelta, 0
);
$localPosDelta += $patchTextLen;
$lineDelta += \substr_count($patchText, "\n");
} elseif ($patchType === 'replace') {

$token->text = substr_replace(
$token->text, $patchText, $patchPos - $pos + $localPosDelta, $patchTextLen
);
} else {
assert(false);
}


$patchIdx++;
if ($patchIdx >= \count($this->patches)) {

$patchPos = \PHP_INT_MAX;
break;
}

list($patchPos, $patchType, $patchText) = $this->patches[$patchIdx];
}

$posDelta += $localPosDelta;
}
return $tokens;
}






private function fixupErrors(array $errors): void {
foreach ($errors as $error) {
$attrs = $error->getAttributes();

$posDelta = 0;
$lineDelta = 0;
foreach ($this->patches as $patch) {
list($patchPos, $patchType, $patchText) = $patch;
if ($patchPos >= $attrs['startFilePos']) {

break;
}

if ($patchType === 'add') {
$posDelta += strlen($patchText);
$lineDelta += substr_count($patchText, "\n");
} elseif ($patchType === 'remove') {
$posDelta -= strlen($patchText);
$lineDelta -= substr_count($patchText, "\n");
}
}

$attrs['startFilePos'] += $posDelta;
$attrs['endFilePos'] += $posDelta;
$attrs['startLine'] += $lineDelta;
$attrs['endLine'] += $lineDelta;
$error->setAttributes($attrs);
}
}
}
