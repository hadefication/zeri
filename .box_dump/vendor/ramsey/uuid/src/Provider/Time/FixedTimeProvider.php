<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;






class FixedTimeProvider implements TimeProviderInterface
{
public function __construct(private Time $time)
{
}






public function setUsec($value): void
{
$this->time = new Time($this->time->getSeconds(), $value);
}






public function setSec($value): void
{
$this->time = new Time($value, $this->time->getMicroseconds());
}

public function getTime(): Time
{
return $this->time;
}
}
