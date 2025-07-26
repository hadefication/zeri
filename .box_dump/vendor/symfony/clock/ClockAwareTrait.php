<?php










namespace Symfony\Component\Clock;

use Psr\Clock\ClockInterface;
use Symfony\Contracts\Service\Attribute\Required;






trait ClockAwareTrait
{
private readonly ClockInterface $clock;

#[Required]
public function setClock(ClockInterface $clock): void
{
$this->clock = $clock;
}

protected function now(): DatePoint
{
$now = ($this->clock ??= new Clock())->now();

return $now instanceof DatePoint ? $now : DatePoint::createFromInterface($now);
}
}
