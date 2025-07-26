<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider;

use Ramsey\Uuid\Type\Hexadecimal;




interface NodeProviderInterface
{





public function getNode(): Hexadecimal;
}
