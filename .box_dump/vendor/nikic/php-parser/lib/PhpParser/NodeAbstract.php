<?php declare(strict_types=1);

namespace PhpParser;

abstract class NodeAbstract implements Node, \JsonSerializable {

protected array $attributes;






public function __construct(array $attributes = []) {
$this->attributes = $attributes;
}

/**
@phpstan-return



*/
public function getLine(): int {
return $this->attributes['startLine'] ?? -1;
}

/**
@phpstan-return





*/
public function getStartLine(): int {
return $this->attributes['startLine'] ?? -1;
}

/**
@phpstan-return





*/
public function getEndLine(): int {
return $this->attributes['endLine'] ?? -1;
}










public function getStartTokenPos(): int {
return $this->attributes['startTokenPos'] ?? -1;
}










public function getEndTokenPos(): int {
return $this->attributes['endTokenPos'] ?? -1;
}








public function getStartFilePos(): int {
return $this->attributes['startFilePos'] ?? -1;
}








public function getEndFilePos(): int {
return $this->attributes['endFilePos'] ?? -1;
}








public function getComments(): array {
return $this->attributes['comments'] ?? [];
}






public function getDocComment(): ?Comment\Doc {
$comments = $this->getComments();
for ($i = count($comments) - 1; $i >= 0; $i--) {
$comment = $comments[$i];
if ($comment instanceof Comment\Doc) {
return $comment;
}
}

return null;
}








public function setDocComment(Comment\Doc $docComment): void {
$comments = $this->getComments();
for ($i = count($comments) - 1; $i >= 0; $i--) {
if ($comments[$i] instanceof Comment\Doc) {

$comments[$i] = $docComment;
$this->setAttribute('comments', $comments);
return;
}
}


$comments[] = $docComment;
$this->setAttribute('comments', $comments);
}

public function setAttribute(string $key, $value): void {
$this->attributes[$key] = $value;
}

public function hasAttribute(string $key): bool {
return array_key_exists($key, $this->attributes);
}

public function getAttribute(string $key, $default = null) {
if (array_key_exists($key, $this->attributes)) {
return $this->attributes[$key];
}

return $default;
}

public function getAttributes(): array {
return $this->attributes;
}

public function setAttributes(array $attributes): void {
$this->attributes = $attributes;
}




public function jsonSerialize(): array {
return ['nodeType' => $this->getType()] + get_object_vars($this);
}
}
