<?php

declare(strict_types=1);

namespace Dotenv\Loader;

use Dotenv\Parser\Entry;
use Dotenv\Parser\Value;
use Dotenv\Repository\RepositoryInterface;

final class Loader implements LoaderInterface
{











public function load(RepositoryInterface $repository, array $entries)
{

return \array_reduce($entries, static function (array $vars, Entry $entry) use ($repository) {
$name = $entry->getName();

$value = $entry->getValue()->map(static function (Value $value) use ($repository) {
return Resolver::resolve($repository, $value);
});

if ($value->isDefined()) {
$inner = $value->get();
if ($repository->set($name, $inner)) {
return \array_merge($vars, [$name => $inner]);
}
} else {
if ($repository->clear($name)) {
return \array_merge($vars, [$name => null]);
}
}

return $vars;
}, []);
}
}
