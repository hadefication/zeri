<?php










namespace Symfony\Component\String\Inflector;

final class SpanishInflector implements InflectorInterface
{








private const PLURALIZE_REGEXP = [

['/(sí|no)$/i', '\1es'],


['/(a|e|i|o|u|á|é|í|ó|ú)$/i', '\1s'],


['/ás$/i', 'ases'],
['/és$/i', 'eses'],
['/ís$/i', 'ises'],
['/ós$/i', 'oses'],
['/ús$/i', 'uses'],


['/ión$/i', '\1iones'],


['/(l|r|n|d|j|s|x|ch|y)$/i', '\1es'],


['/(z)$/i', 'ces'],
];




private const SINGULARIZE_REGEXP = [

['/(sí|no)es$/i', '\1'],


['/iones$/i', '\1ión'],


['/ces$/i', 'z'],


['/(\w)ases$/i', '\1ás'],
['/eses$/i', 'és'],
['/ises$/i', 'ís'],
['/(\w{2,})oses$/i', '\1ós'],
['/(\w)uses$/i', '\1ús'],


['/(l|r|n|d|j|s|x|ch|y)e?s$/i', '\1'],


['/(a|e|i|o|u|á|é|ó|í|ú)s$/i', '\1'],
];

private const UNINFLECTED_RULES = [

'/.*(piés)$/i',
];

private const UNINFLECTED = '/^(lunes|martes|miércoles|jueves|viernes|análisis|torax|yo|pies)$/i';

public function singularize(string $plural): array
{
if ($this->isInflectedWord($plural)) {
return [$plural];
}

foreach (self::SINGULARIZE_REGEXP as $rule) {
[$regexp, $replace] = $rule;

if (1 === preg_match($regexp, $plural)) {
return [preg_replace($regexp, $replace, $plural)];
}
}

return [$plural];
}

public function pluralize(string $singular): array
{
if ($this->isInflectedWord($singular)) {
return [$singular];
}

foreach (self::PLURALIZE_REGEXP as $rule) {
[$regexp, $replace] = $rule;

if (1 === preg_match($regexp, $singular)) {
return [preg_replace($regexp, $replace, $singular)];
}
}

return [$singular.'s'];
}

private function isInflectedWord(string $word): bool
{
foreach (self::UNINFLECTED_RULES as $rule) {
if (1 === preg_match($rule, $word)) {
return true;
}
}

return 1 === preg_match(self::UNINFLECTED, $word);
}
}
