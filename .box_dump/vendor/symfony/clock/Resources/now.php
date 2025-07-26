<?php










namespace Symfony\Component\Clock;

if (!\function_exists(now::class)) {



function now(string $modifier = 'now'): DatePoint
{
if ('now' !== $modifier) {
return new DatePoint($modifier);
}

$now = Clock::get()->now();

return $now instanceof DatePoint ? $now : DatePoint::createFromInterface($now);
}
}
