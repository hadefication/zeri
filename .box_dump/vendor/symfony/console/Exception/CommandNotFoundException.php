<?php










namespace Symfony\Component\Console\Exception;






class CommandNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{






public function __construct(
string $message,
private array $alternatives = [],
int $code = 0,
?\Throwable $previous = null,
) {
parent::__construct($message, $code, $previous);
}




public function getAlternatives(): array
{
return $this->alternatives;
}
}
