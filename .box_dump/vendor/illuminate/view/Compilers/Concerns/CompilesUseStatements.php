<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesUseStatements
{






protected function compileUse($expression)
{
$expression = trim(preg_replace('/[()]/', '', $expression), " '\"");


if (str_contains($expression, '{')) {
$pathWithOptionalModifier = $expression;
$aliasWithLeadingSpace = '';
} else {
$segments = explode(',', $expression);
$pathWithOptionalModifier = trim($segments[0], " '\"");

$aliasWithLeadingSpace = isset($segments[1])
? ' as '.trim($segments[1], " '\"")
: '';
}


if (str_starts_with($pathWithOptionalModifier, 'function ')) {
$modifierWithTrailingSpace = 'function ';
$path = explode(' ', $pathWithOptionalModifier, 2)[1] ?? $pathWithOptionalModifier;
} elseif (str_starts_with($pathWithOptionalModifier, 'const ')) {
$modifierWithTrailingSpace = 'const ';
$path = explode(' ', $pathWithOptionalModifier, 2)[1] ?? $pathWithOptionalModifier;
} else {
$modifierWithTrailingSpace = '';
$path = $pathWithOptionalModifier;
}

$path = ltrim($path, '\\');

return "<?php use {$modifierWithTrailingSpace}\\{$path}{$aliasWithLeadingSpace}; ?>";
}
}
