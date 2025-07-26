<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Type\Hexadecimal;




interface TimeGeneratorInterface
{










public function generate($node = null, ?int $clockSeq = null): string;
}
