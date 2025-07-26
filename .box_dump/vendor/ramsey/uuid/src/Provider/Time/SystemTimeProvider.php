<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Type\Time;

use function gettimeofday;




class SystemTimeProvider implements TimeProviderInterface
{
public function getTime(): Time
{
$time = gettimeofday();

return new Time($time['sec'], $time['usec']);
}
}
