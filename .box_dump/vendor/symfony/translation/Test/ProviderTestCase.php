<?php










namespace Symfony\Component\Translation\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Provider\ProviderInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;






abstract class ProviderTestCase extends TestCase
{
protected HttpClientInterface $client;
protected LoggerInterface|MockObject $logger;
protected string $defaultLocale;
protected LoaderInterface|MockObject $loader;
protected XliffFileDumper|MockObject $xliffFileDumper;
protected TranslatorBagInterface|MockObject $translatorBag;

abstract public static function createProvider(HttpClientInterface $client, LoaderInterface $loader, LoggerInterface $logger, string $defaultLocale, string $endpoint): ProviderInterface;




abstract public static function toStringProvider(): iterable;

/**
@dataProvider
*/
#[DataProvider('toStringProvider')]
public function testToString(ProviderInterface $provider, string $expected)
{
$this->assertSame($expected, (string) $provider);
}

protected function getClient(): MockHttpClient
{
return $this->client ??= new MockHttpClient();
}

protected function getLoader(): LoaderInterface
{
return $this->loader ??= $this->createMock(LoaderInterface::class);
}

protected function getLogger(): LoggerInterface
{
return $this->logger ??= $this->createMock(LoggerInterface::class);
}

protected function getDefaultLocale(): string
{
return $this->defaultLocale ??= 'en';
}

protected function getXliffFileDumper(): XliffFileDumper
{
return $this->xliffFileDumper ??= $this->createMock(XliffFileDumper::class);
}

protected function getTranslatorBag(): TranslatorBagInterface
{
return $this->translatorBag ??= $this->createMock(TranslatorBagInterface::class);
}
}
