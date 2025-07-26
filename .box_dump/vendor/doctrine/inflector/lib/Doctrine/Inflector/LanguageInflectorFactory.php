<?php

declare(strict_types=1);

namespace Doctrine\Inflector;

use Doctrine\Inflector\Rules\Ruleset;

interface LanguageInflectorFactory
{







public function withSingularRules(?Ruleset $singularRules, bool $reset = false): self;








public function withPluralRules(?Ruleset $pluralRules, bool $reset = false): self;




public function build(): Inflector;
}
