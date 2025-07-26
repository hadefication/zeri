<?php










namespace Symfony\Component\Translation\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Translation\Exception\IncompleteDsnException;
use Symfony\Component\Translation\Provider\Dsn;

trait IncompleteDsnTestTrait
{



abstract public static function incompleteDsnProvider(): iterable;

/**
@dataProvider
*/
#[DataProvider('incompleteDsnProvider')]
public function testIncompleteDsnException(string $dsn, ?string $message = null)
{
$factory = $this->createFactory();

$dsn = new Dsn($dsn);

$this->expectException(IncompleteDsnException::class);
if (null !== $message) {
$this->expectExceptionMessage($message);
}

$factory->create($dsn);
}
}
