<?php










namespace Symfony\Component\Translation\Exception;




interface ProviderExceptionInterface extends ExceptionInterface
{



public function getDebug(): string;
}
