<?php declare(strict_types=1);

namespace PhpParser;

interface NodeTraverserInterface {





public function addVisitor(NodeVisitor $visitor): void;




public function removeVisitor(NodeVisitor $visitor): void;








public function traverse(array $nodes): array;
}
