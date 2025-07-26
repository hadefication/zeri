<?php declare(strict_types=1);

namespace PhpParser;

class Error extends \RuntimeException {
protected string $rawMessage;

protected array $attributes;







public function __construct(string $message, array $attributes = []) {
$this->rawMessage = $message;
$this->attributes = $attributes;
$this->updateMessage();
}






public function getRawMessage(): string {
return $this->rawMessage;
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






public function getAttributes(): array {
return $this->attributes;
}






public function setAttributes(array $attributes): void {
$this->attributes = $attributes;
$this->updateMessage();
}






public function setRawMessage(string $message): void {
$this->rawMessage = $message;
$this->updateMessage();
}






public function setStartLine(int $line): void {
$this->attributes['startLine'] = $line;
$this->updateMessage();
}






public function hasColumnInfo(): bool {
return isset($this->attributes['startFilePos'], $this->attributes['endFilePos']);
}






public function getStartColumn(string $code): int {
if (!$this->hasColumnInfo()) {
throw new \RuntimeException('Error does not have column information');
}

return $this->toColumn($code, $this->attributes['startFilePos']);
}






public function getEndColumn(string $code): int {
if (!$this->hasColumnInfo()) {
throw new \RuntimeException('Error does not have column information');
}

return $this->toColumn($code, $this->attributes['endFilePos']);
}








public function getMessageWithColumnInfo(string $code): string {
return sprintf(
'%s from %d:%d to %d:%d', $this->getRawMessage(),
$this->getStartLine(), $this->getStartColumn($code),
$this->getEndLine(), $this->getEndColumn($code)
);
}









private function toColumn(string $code, int $pos): int {
if ($pos > strlen($code)) {
throw new \RuntimeException('Invalid position information');
}

$lineStartPos = strrpos($code, "\n", $pos - strlen($code));
if (false === $lineStartPos) {
$lineStartPos = -1;
}

return $pos - $lineStartPos;
}




protected function updateMessage(): void {
$this->message = $this->rawMessage;

if (-1 === $this->getStartLine()) {
$this->message .= ' on unknown line';
} else {
$this->message .= ' on line ' . $this->getStartLine();
}
}
}
