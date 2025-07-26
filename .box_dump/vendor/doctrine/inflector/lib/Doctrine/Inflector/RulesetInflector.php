<?php

declare(strict_types=1);

namespace Doctrine\Inflector;

use Doctrine\Inflector\Rules\Ruleset;

use function array_merge;










class RulesetInflector implements WordInflector
{

private $rulesets;

public function __construct(Ruleset $ruleset, Ruleset ...$rulesets)
{
$this->rulesets = array_merge([$ruleset], $rulesets);
}

public function inflect(string $word): string
{
if ($word === '') {
return '';
}

foreach ($this->rulesets as $ruleset) {
if ($ruleset->getUninflected()->matches($word)) {
return $word;
}

$inflected = $ruleset->getIrregular()->inflect($word);

if ($inflected !== $word) {
return $inflected;
}

$inflected = $ruleset->getRegular()->inflect($word);

if ($inflected !== $word) {
return $inflected;
}
}

return $word;
}
}
