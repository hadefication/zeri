<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider;

use Ramsey\Uuid\Type\Time;




interface TimeProviderInterface
{



public function getTime(): Time;
}
