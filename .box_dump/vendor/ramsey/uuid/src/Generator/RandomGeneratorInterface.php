<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;




interface RandomGeneratorInterface
{







public function generate(int $length): string;
}
