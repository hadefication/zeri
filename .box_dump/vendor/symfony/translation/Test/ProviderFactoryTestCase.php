<?php










namespace Symfony\Component\Translation\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;








abstract class ProviderFactoryTestCase extends AbstractProviderFactoryTestCase
{
use IncompleteDsnTestTrait;

protected HttpClientInterface $client;
protected LoggerInterface|MockObject $logger;
protected string $defaultLocale;
protected LoaderInterface|MockObject $loader;
protected XliffFileDumper|MockObject $xliffFileDumper;
protected TranslatorBagInterface|MockObject $translatorBag;




public static function unsupportedSchemeProvider(): iterable
{
return [];
}




public static function incompleteDsnProvider(): iterable
{
return [];
}

protected function getClient(): HttpClientInterface
{
return $this->client ??= new MockHttpClient();
}

protected function getLogger(): LoggerInterface
{
return $this->logger ??= $this->createMock(LoggerInterface::class);
}

protected function getDefaultLocale(): string
{
return $this->defaultLocale ??= 'en';
}

protected function getLoader(): LoaderInterface
{
return $this->loader ??= $this->createMock(LoaderInterface::class);
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
