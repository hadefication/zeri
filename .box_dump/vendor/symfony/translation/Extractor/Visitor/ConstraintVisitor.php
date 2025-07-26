<?php










namespace Symfony\Component\Translation\Extractor\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitor;






final class ConstraintVisitor extends AbstractVisitor implements NodeVisitor
{
public function __construct(
private readonly array $constraintClassNames = [],
) {
}

public function beforeTraverse(array $nodes): ?Node
{
return null;
}

public function enterNode(Node $node): ?Node
{
return null;
}

public function leaveNode(Node $node): ?Node
{
if (!$node instanceof Node\Expr\New_ && !$node instanceof Node\Attribute) {
return null;
}

$className = $node instanceof Node\Attribute ? $node->name : $node->class;
if (!$className instanceof Node\Name) {
return null;
}

$parts = $className->getParts();
$isConstraintClass = false;

foreach ($parts as $part) {
if (\in_array($part, $this->constraintClassNames, true)) {
$isConstraintClass = true;

break;
}
}

if (!$isConstraintClass) {
return null;
}

$arg = $node->args[0] ?? null;
if (!$arg instanceof Node\Arg) {
return null;
}

if ($this->hasNodeNamedArguments($node)) {
$messages = $this->getStringArguments($node, '/message/i', true);
} else {
if (!$arg->value instanceof Node\Expr\Array_) {

return null;
}

$messages = [];
$options = $arg->value;


foreach ($options->items as $item) {
if (!$item->key instanceof Node\Scalar\String_) {
continue;
}

if (false === stripos($item->key->value ?? '', 'message')) {
continue;
}

if (!$item->value instanceof Node\Scalar\String_) {
continue;
}

$messages[] = $item->value->value;

break;
}
}

foreach ($messages as $message) {
$this->addMessageToCatalogue($message, 'validators', $node->getStartLine());
}

return null;
}

public function afterTraverse(array $nodes): ?Node
{
return null;
}
}
