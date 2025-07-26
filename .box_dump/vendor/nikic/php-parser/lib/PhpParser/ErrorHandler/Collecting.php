<?php declare(strict_types=1);

namespace PhpParser\ErrorHandler;

use PhpParser\Error;
use PhpParser\ErrorHandler;






class Collecting implements ErrorHandler {

private array $errors = [];

public function handleError(Error $error): void {
$this->errors[] = $error;
}






public function getErrors(): array {
return $this->errors;
}




public function hasErrors(): bool {
return !empty($this->errors);
}




public function clearErrors(): void {
$this->errors = [];
}
}
