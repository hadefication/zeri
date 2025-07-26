<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use RandomLib\Factory;
use RandomLib\Generator;









class RandomLibAdapter implements RandomGeneratorInterface
{
private Generator $generator;









public function __construct(?Generator $generator = null)
{
if ($generator === null) {
$factory = new Factory();
$generator = $factory->getHighStrengthGenerator();
}

$this->generator = $generator;
}

public function generate(int $length): string
{
return $this->generator->generate($length);
}
}
