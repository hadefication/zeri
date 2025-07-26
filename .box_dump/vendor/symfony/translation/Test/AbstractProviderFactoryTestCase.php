<?php










namespace Symfony\Component\Translation\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Exception\UnsupportedSchemeException;
use Symfony\Component\Translation\Provider\Dsn;
use Symfony\Component\Translation\Provider\ProviderFactoryInterface;

abstract class AbstractProviderFactoryTestCase extends TestCase
{
abstract public function createFactory(): ProviderFactoryInterface;




abstract public static function supportsProvider(): iterable;




abstract public static function createProvider(): iterable;




abstract public static function unsupportedSchemeProvider(): iterable;

/**
@dataProvider
*/
#[DataProvider('supportsProvider')]
public function testSupports(bool $expected, string $dsn)
{
$factory = $this->createFactory();

$this->assertSame($expected, $factory->supports(new Dsn($dsn)));
}

/**
@dataProvider
*/
#[DataProvider('createProvider')]
public function testCreate(string $expected, string $dsn)
{
$factory = $this->createFactory();
$provider = $factory->create(new Dsn($dsn));

$this->assertSame($expected, (string) $provider);
}

/**
@dataProvider
*/
#[DataProvider('unsupportedSchemeProvider')]
public function testUnsupportedSchemeException(string $dsn, ?string $message = null)
{
$factory = $this->createFactory();

$dsn = new Dsn($dsn);

$this->expectException(UnsupportedSchemeException::class);
if (null !== $message) {
$this->expectExceptionMessage($message);
}

$factory->create($dsn);
}
}
