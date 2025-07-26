<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use function array_keys;
use function get_object_vars;
use function is_int;
use function sprintf;
use RuntimeException;
use Throwable;

/**
@no-named-arguments





















*/
class Exception extends RuntimeException implements \PHPUnit\Exception
{



protected array $serializableTrace;

public function __construct(string $message = '', int|string $code = 0, ?Throwable $previous = null)
{



if (!is_int($code)) {
$message .= sprintf(
' (exception code: %s)',
$code,
);

$code = 0;
}

parent::__construct($message, $code, $previous);

$this->serializableTrace = $this->getTrace();

foreach (array_keys($this->serializableTrace) as $key) {
unset($this->serializableTrace[$key]['args']);
}
}

public function __sleep(): array
{
return array_keys(get_object_vars($this));
}






public function getSerializableTrace(): array
{
return $this->serializableTrace;
}
}
