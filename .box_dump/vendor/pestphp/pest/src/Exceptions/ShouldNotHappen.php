<?php

declare(strict_types=1);

namespace Pest\Exceptions;

use Exception;
use RuntimeException;




final class ShouldNotHappen extends RuntimeException
{



public function __construct(Exception $exception)
{
$message = $exception->getMessage();

parent::__construct(sprintf(<<<'EOF'
This should not happen - please create an new issue here: https://github.com/pestphp/pest.

  Issue: %s
  PHP version: %s
  Operating system: %s
EOF
, $message, phpversion(), PHP_OS), 1, $exception);
}




public static function fromMessage(string $message): ShouldNotHappen
{
return new ShouldNotHappen(new Exception($message));
}
}
