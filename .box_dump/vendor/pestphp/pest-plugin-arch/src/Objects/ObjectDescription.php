<?php

declare(strict_types=1);

namespace Pest\Arch\Objects;

use Pest\Arch\Support\PhpCoreExpressions;
use PhpParser\Node\Expr;
use PHPUnit\Architecture\Asserts\Dependencies\Elements\ObjectUses;
use PHPUnit\Architecture\Services\ServiceContainer;




final class ObjectDescription extends \PHPUnit\Architecture\Elements\ObjectDescription
{



public static function make(string $path): ?self
{

$description = parent::make($path);

if (! $description instanceof \Pest\Arch\Objects\ObjectDescription) {
return null;
}

$description->uses = new ObjectUses(
[
...$description->uses->getIterator(),
...self::retrieveCoreUses($description),
]
);

return $description;
}




private static function retrieveCoreUses(ObjectDescription $description): array
{

$expressions = [];

foreach (PhpCoreExpressions::$ENABLED as $expression) {
$expressions = [
...$expressions,
...ServiceContainer::$nodeFinder->findInstanceOf(
$description->stmts,
$expression,
),
];
}


return array_filter(array_map(fn (Expr $expression): string => PhpCoreExpressions::getName($expression), $expressions));
}
}
