<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Factories\NodeTraverserFactory;
use Pest\Mutate\Mutation;
use Pest\Support\Str;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use Symfony\Component\Finder\SplFileInfo;

class MutationGenerator
{
private bool $mutated;

private int $offset;

private Node $originalNode; 

private ?Node $modifiedNode = null; 







public function generate(
SplFileInfo $file,
array $mutators,
array $linesToMutate = [],
array $classesToMutate = [],
): array {
$mutations = [];

$contents = $file->getContents();

if ($this->doesNotContainClassToMutate($contents, $classesToMutate)) {
return $mutations;
}

$mutatorsToIgnoreByLine = [];
foreach (explode(PHP_EOL, $contents) as $lineNumber => $line) {
if (str_contains($line, '@pest-mutate-ignore')) {
if (Str::after($line, '@pest-mutate-ignore:') !== $line) {
$mutatorsToIgnore = explode(',', Str::after($line, '@pest-mutate-ignore:'));
$mutatorsToIgnore = array_map(fn (string $mutator): string => trim($mutator), $mutatorsToIgnore);
}
$mutatorsToIgnoreByLine[$lineNumber + 1] = $mutatorsToIgnore ?? ['all'];
}
}

$parser = PhpParserFactory::make();

$mutators = $this->filterMutators($mutators, $contents, $parser);

$cache = MutationCache::instance();
foreach ($mutators as $mutator) {
if ($cache->has($file, $contents, $linesToMutate, $mutator)) {
$newMutations = $cache->get($file, $contents, $linesToMutate, $mutator);
} else {
$newMutations = [];

$this->offset = 0; 

while (true) {
$this->mutated = false;

$traverser = NodeTraverserFactory::create();
$traverser->addVisitor(new NodeVisitor(
mutator: $mutator,
linesToMutate: $linesToMutate,
offset: $this->offset,
mutatorsToIgnoreByLine: $mutatorsToIgnoreByLine,
hasAlreadyMutated: $this->hasMutated(...),
trackMutation: $this->trackMutation(...),
));

$stmts = $parser->parse($contents);

assert($stmts !== null);

$modifiedAst = $traverser->traverse($stmts);

if (! $this->mutated) { 
break;
}

$newMutations[] = Mutation::create( 
file: $file,
mutator: $mutator,
originalNode: $this->originalNode,
modifiedNode: $this->modifiedNode,
modifiedAst: $modifiedAst,
);
}

$cache->put($file, $contents, $linesToMutate, $mutator, $newMutations);
}

$mutations = [
...$mutations,
...$newMutations,
];
}













usort($mutations, fn (Mutation $a, Mutation $b): int => $a->startLine <=> $b->startLine);

return $mutations;
}

private function trackMutation(int $nodeCount, Node $original, ?Node $modified): void
{
$this->mutated = true;
$this->offset = $nodeCount;
$this->originalNode = $original;
$this->modifiedNode = $modified;
}

private function hasMutated(): bool
{
return $this->mutated;
}




private function doesNotContainClassToMutate(string $contents, array $classesToMutate): bool
{
if ($classesToMutate === []) {
return false;
}

foreach ($classesToMutate as $classOrNamespace) {
$parts = explode('\\', $classOrNamespace);
$class = array_pop($parts);
$namespace = preg_quote(implode('\\', $parts));
$classOrNamespace = preg_quote($classOrNamespace);

if (preg_match("/namespace\\s+$namespace/", $contents) === 1 && preg_match("/(?:class|trait)\\s+$class.*/", $contents) === 1) {
return false;
}

if (preg_match("/(?:class|trait)\\s+$class\[{\\s*\]/", $contents) === 1) {
return false;
}

if (preg_match("/namespace\\s+$classOrNamespace/", $contents) === 1) {
return false;
}
}

return true;
}





private function filterMutators(array $mutators, string $contents, Parser $parser): array
{
$nodeTypes = [];

$traverser = new NodeTraverser;
$traverser->addVisitor(new class(function (string $nodeType) use (&$nodeTypes): string {
return $nodeTypes[] = $nodeType;
}) extends NodeVisitorAbstract {




public function __construct(private $callback) 
{}

public function enterNode(Node $node): ?Node
{
($this->callback)($node::class);

return null;
}
});
$traverser->traverse($parser->parse($contents)); 

$nodeTypes = array_unique($nodeTypes);

$mutatorsToUse = [];
foreach ($nodeTypes as $nodeType) {
foreach (MutatorMap::get()[$nodeType] ?? [] as $mutator) {
$mutatorsToUse[] = $mutator;
}
}

return array_intersect($mutators, $mutatorsToUse);
}
}
