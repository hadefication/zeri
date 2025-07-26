<?php declare(strict_types=1);

namespace PhpParser;

interface Node {
/**
@psalm-return



*/
public function getType(): string;






public function getSubNodeNames(): array;

/**
@phpstan-return





*/
public function getLine(): int;

/**
@phpstan-return





*/
public function getStartLine(): int;

/**
@phpstan-return





*/
public function getEndLine(): int;










public function getStartTokenPos(): int;










public function getEndTokenPos(): int;








public function getStartFilePos(): int;








public function getEndFilePos(): int;








public function getComments(): array;






public function getDocComment(): ?Comment\Doc;








public function setDocComment(Comment\Doc $docComment): void;






public function setAttribute(string $key, $value): void;




public function hasAttribute(string $key): bool;








public function getAttribute(string $key, $default = null);






public function getAttributes(): array;






public function setAttributes(array $attributes): void;
}
