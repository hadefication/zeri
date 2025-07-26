<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;




class RandomGeneratorFactory
{



public function getGenerator(): RandomGeneratorInterface
{
return new RandomBytesGenerator();
}
}
