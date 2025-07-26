<?php declare(strict_types=1);

namespace PhpParser;

if (!\function_exists('PhpParser\defineCompatibilityTokens')) {
function defineCompatibilityTokens(): void {
$compatTokens = [

'T_NAME_QUALIFIED',
'T_NAME_FULLY_QUALIFIED',
'T_NAME_RELATIVE',
'T_MATCH',
'T_NULLSAFE_OBJECT_OPERATOR',
'T_ATTRIBUTE',

'T_ENUM',
'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG',
'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG',
'T_READONLY',

'T_PROPERTY_C',
'T_PUBLIC_SET',
'T_PROTECTED_SET',
'T_PRIVATE_SET',
];




$usedTokenIds = [];
foreach ($compatTokens as $token) {
if (\defined($token)) {
$tokenId = \constant($token);
if (!\is_int($tokenId)) {
throw new \Error(sprintf(
'Token %s has ID of type %s, should be int. ' .
'You may be using a library with broken token emulation',
$token, \gettype($tokenId)
));
}
$clashingToken = $usedTokenIds[$tokenId] ?? null;
if ($clashingToken !== null) {
throw new \Error(sprintf(
'Token %s has same ID as token %s, ' .
'you may be using a library with broken token emulation',
$token, $clashingToken
));
}
$usedTokenIds[$tokenId] = $token;
}
}



$newTokenId = -1;
foreach ($compatTokens as $token) {
if (!\defined($token)) {
while (isset($usedTokenIds[$newTokenId])) {
$newTokenId--;
}
\define($token, $newTokenId);
$newTokenId--;
}
}
}

defineCompatibilityTokens();
}
