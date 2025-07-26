<?php











declare(strict_types=1);

namespace Ramsey\Uuid;

use Ramsey\Uuid\Builder\FallbackBuilder;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\GuidStringCodec;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\DceSecurityGenerator;
use Ramsey\Uuid\Generator\DceSecurityGeneratorInterface;
use Ramsey\Uuid\Generator\NameGeneratorFactory;
use Ramsey\Uuid\Generator\NameGeneratorInterface;
use Ramsey\Uuid\Generator\PeclUuidNameGenerator;
use Ramsey\Uuid\Generator\PeclUuidRandomGenerator;
use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorFactory;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Nonstandard\UuidBuilder as NonstandardUuidBuilder;
use Ramsey\Uuid\Provider\Dce\SystemDceSecurityProvider;
use Ramsey\Uuid\Provider\DceSecurityProviderInterface;
use Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Provider\Node\SystemNodeProvider;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\SystemTimeProvider;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Ramsey\Uuid\Validator\GenericValidator;
use Ramsey\Uuid\Validator\ValidatorInterface;

use const PHP_INT_SIZE;






class FeatureSet
{
private ?TimeProviderInterface $timeProvider = null;
private CalculatorInterface $calculator;
private CodecInterface $codec;
private DceSecurityGeneratorInterface $dceSecurityGenerator;
private NameGeneratorInterface $nameGenerator;
private NodeProviderInterface $nodeProvider;
private NumberConverterInterface $numberConverter;
private RandomGeneratorInterface $randomGenerator;
private TimeConverterInterface $timeConverter;
private TimeGeneratorInterface $timeGenerator;
private TimeGeneratorInterface $unixTimeGenerator;
private UuidBuilderInterface $builder;
private ValidatorInterface $validator;

/**
@phpstan-ignore






*/
public function __construct(
bool $useGuids = false,
private bool $force32Bit = false,
bool $forceNoBigNumber = false,
private bool $ignoreSystemNode = false,
private bool $enablePecl = false,
) {
$this->randomGenerator = $this->buildRandomGenerator();
$this->setCalculator(new BrickMathCalculator());
$this->builder = $this->buildUuidBuilder($useGuids);
$this->codec = $this->buildCodec($useGuids);
$this->nodeProvider = $this->buildNodeProvider();
$this->nameGenerator = $this->buildNameGenerator();
$this->setTimeProvider(new SystemTimeProvider());
$this->setDceSecurityProvider(new SystemDceSecurityProvider());
$this->validator = new GenericValidator();

assert($this->timeProvider !== null);
$this->unixTimeGenerator = $this->buildUnixTimeGenerator();
}




public function getBuilder(): UuidBuilderInterface
{
return $this->builder;
}




public function getCalculator(): CalculatorInterface
{
return $this->calculator;
}




public function getCodec(): CodecInterface
{
return $this->codec;
}




public function getDceSecurityGenerator(): DceSecurityGeneratorInterface
{
return $this->dceSecurityGenerator;
}




public function getNameGenerator(): NameGeneratorInterface
{
return $this->nameGenerator;
}




public function getNodeProvider(): NodeProviderInterface
{
return $this->nodeProvider;
}




public function getNumberConverter(): NumberConverterInterface
{
return $this->numberConverter;
}




public function getRandomGenerator(): RandomGeneratorInterface
{
return $this->randomGenerator;
}




public function getTimeConverter(): TimeConverterInterface
{
return $this->timeConverter;
}




public function getTimeGenerator(): TimeGeneratorInterface
{
return $this->timeGenerator;
}




public function getUnixTimeGenerator(): TimeGeneratorInterface
{
return $this->unixTimeGenerator;
}




public function getValidator(): ValidatorInterface
{
return $this->validator;
}




public function setCalculator(CalculatorInterface $calculator): void
{
$this->calculator = $calculator;
$this->numberConverter = $this->buildNumberConverter($calculator);
$this->timeConverter = $this->buildTimeConverter($calculator);

if (isset($this->timeProvider)) {
$this->timeGenerator = $this->buildTimeGenerator($this->timeProvider);
}
}




public function setDceSecurityProvider(DceSecurityProviderInterface $dceSecurityProvider): void
{
$this->dceSecurityGenerator = $this->buildDceSecurityGenerator($dceSecurityProvider);
}




public function setNodeProvider(NodeProviderInterface $nodeProvider): void
{
$this->nodeProvider = $nodeProvider;

if (isset($this->timeProvider)) {
$this->timeGenerator = $this->buildTimeGenerator($this->timeProvider);
}
}




public function setTimeProvider(TimeProviderInterface $timeProvider): void
{
$this->timeProvider = $timeProvider;
$this->timeGenerator = $this->buildTimeGenerator($timeProvider);
}




public function setValidator(ValidatorInterface $validator): void
{
$this->validator = $validator;
}






private function buildCodec(bool $useGuids = false): CodecInterface
{
if ($useGuids) {
return new GuidStringCodec($this->builder);
}

return new StringCodec($this->builder);
}




private function buildDceSecurityGenerator(
DceSecurityProviderInterface $dceSecurityProvider,
): DceSecurityGeneratorInterface {
return new DceSecurityGenerator($this->numberConverter, $this->timeGenerator, $dceSecurityProvider);
}




private function buildNodeProvider(): NodeProviderInterface
{
if ($this->ignoreSystemNode) {
return new RandomNodeProvider();
}

return new FallbackNodeProvider([new SystemNodeProvider(), new RandomNodeProvider()]);
}




private function buildNumberConverter(CalculatorInterface $calculator): NumberConverterInterface
{
return new GenericNumberConverter($calculator);
}




private function buildRandomGenerator(): RandomGeneratorInterface
{
if ($this->enablePecl) {
return new PeclUuidRandomGenerator();
}

return (new RandomGeneratorFactory())->getGenerator();
}







private function buildTimeGenerator(TimeProviderInterface $timeProvider): TimeGeneratorInterface
{
if ($this->enablePecl) {
return new PeclUuidTimeGenerator();
}

return (new TimeGeneratorFactory($this->nodeProvider, $this->timeConverter, $timeProvider))->getGenerator();
}




private function buildUnixTimeGenerator(): TimeGeneratorInterface
{
return new UnixTimeGenerator($this->randomGenerator);
}




private function buildNameGenerator(): NameGeneratorInterface
{
if ($this->enablePecl) {
return new PeclUuidNameGenerator();
}

return (new NameGeneratorFactory())->getGenerator();
}




private function buildTimeConverter(CalculatorInterface $calculator): TimeConverterInterface
{
$genericConverter = new GenericTimeConverter($calculator);

if ($this->is64BitSystem()) {
return new PhpTimeConverter($calculator, $genericConverter);
}

return $genericConverter;
}






private function buildUuidBuilder(bool $useGuids = false): UuidBuilderInterface
{
if ($useGuids) {
return new GuidBuilder($this->numberConverter, $this->timeConverter);
}

return new FallbackBuilder([
new Rfc4122UuidBuilder($this->numberConverter, $this->timeConverter),
new NonstandardUuidBuilder($this->numberConverter, $this->timeConverter),
]);
}




private function is64BitSystem(): bool
{
return PHP_INT_SIZE === 8 && !$this->force32Bit;
}
}
