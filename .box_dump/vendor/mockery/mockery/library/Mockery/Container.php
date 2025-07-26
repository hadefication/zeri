<?php









namespace Mockery;

use Closure;
use Exception as PHPException;
use Mockery;
use Mockery\Exception\InvalidOrderException;
use Mockery\Exception\RuntimeException;
use Mockery\Generator\Generator;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Loader\Loader as LoaderInterface;
use ReflectionClass;
use ReflectionException;
use Throwable;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_pop;
use function array_shift;
use function array_values;
use function class_exists;
use function count;
use function explode;
use function get_class;
use function interface_exists;
use function is_callable;
use function is_object;
use function is_string;
use function md5;
use function preg_grep;
use function preg_match;
use function range;
use function reset;
use function rtrim;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function trait_exists;

/**
@template


*/
class Container
{
public const BLOCKS = Mockery::BLOCKS;






protected $_allocatedOrder = 0;






protected $_currentOrder = 0;




protected $_generator;






protected $_groups = [];




protected $_loader;






protected $_mocks = [];




protected $_namedMocks = [];




protected $instantiator;

public function __construct(?Generator $generator = null, ?LoaderInterface $loader = null, ?Instantiator $instantiator = null)
{
$this->_generator = $generator instanceof Generator ? $generator : Mockery::getDefaultGenerator();
$this->_loader = $loader instanceof LoaderInterface ? $loader : Mockery::getDefaultLoader();
$this->instantiator = $instantiator instanceof Instantiator ? $instantiator : new Instantiator();
}

/**
@template







*/
public function fetchMock($reference)
{
return $this->_mocks[$reference] ?? null;
}




public function getGenerator()
{
return $this->_generator;
}







public function getKeyOfDemeterMockFor($method, $parent)
{
$keys = array_keys($this->_mocks);

$match = preg_grep('/__demeter_' . md5($parent) . sprintf('_%s$/', $method), $keys);
if ($match === false) {
return null;
}

if ($match === []) {
return null;
}

return array_values($match)[0];
}




public function getLoader()
{
return $this->_loader;
}

/**
@template

*/
public function getMocks()
{
return $this->_mocks;
}




public function instanceMock()
{
}








public function isValidClassName($className)
{
if ($className[0] === '\\') {
$className = substr($className, 1); 
}


return array_filter(
explode('\\', $className),
static function ($name): bool {
return ! preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name);
}
) === [];
}

/**
@template













*/
public function mock(...$args)
{

$builder = null;

$expectationClosure = null;
$partialMethods = null;
$quickDefinitions = [];
$constructorArgs = null;
$blocks = [];

if (count($args) > 1) {
$finalArg = array_pop($args);

if (is_callable($finalArg) && is_object($finalArg)) {
$expectationClosure = $finalArg;
} else {
$args[] = $finalArg;
}
}

foreach ($args as $k => $arg) {
if ($arg instanceof MockConfigurationBuilder) {
$builder = $arg;

unset($args[$k]);
}
}

reset($args);

$builder = $builder ?? new MockConfigurationBuilder();
$mockeryConfiguration = Mockery::getConfiguration();
$builder->setParameterOverrides($mockeryConfiguration->getInternalClassMethodParamMaps());
$builder->setConstantsMap($mockeryConfiguration->getConstantsMap());

while ($args !== []) {
$arg = array_shift($args);


if (is_string($arg)) {
foreach (explode('|', $arg) as $type) {
if ($arg === 'null') {

continue;
}

if (strpos($type, ',') && !strpos($type, ']')) {
$interfaces = explode(',', str_replace(' ', '', $type));

$builder->addTargets($interfaces);

continue;
}

if (strpos($type, 'alias:') === 0) {
$type = str_replace('alias:', '', $type);

$builder->addTarget('stdClass');
$builder->setName($type);

continue;
}

if (strpos($type, 'overload:') === 0) {
$type = str_replace('overload:', '', $type);

$builder->setInstanceMock(true);
$builder->addTarget('stdClass');
$builder->setName($type);

continue;
}

if ($type[strlen($type) - 1] === ']') {
$parts = explode('[', $type);

$class = $parts[0];

if (! class_exists($class, true) && ! interface_exists($class, true)) {
throw new Exception('Can only create a partial mock from an existing class or interface');
}

$builder->addTarget($class);

$partialMethods = array_filter(
explode(',', strtolower(rtrim(str_replace(' ', '', $parts[1]), ']')))
);

foreach ($partialMethods as $partialMethod) {
if ($partialMethod[0] === '!') {
$builder->addBlackListedMethod(substr($partialMethod, 1));

continue;
}

$builder->addWhiteListedMethod($partialMethod);
}

continue;
}

if (class_exists($type, true) || interface_exists($type, true) || trait_exists($type, true)) {
$builder->addTarget($type);

continue;
}

if (! $mockeryConfiguration->mockingNonExistentMethodsAllowed()) {
throw new Exception(sprintf("Mockery can't find '%s' so can't mock it", $type));
}

if (! $this->isValidClassName($type)) {
throw new Exception('Class name contains invalid characters');
}

$builder->addTarget($type);


break;
}

continue;
}

if (is_object($arg)) {
$builder->addTarget($arg);

continue;
}

if (is_array($arg)) {
if ([] !== $arg && array_keys($arg) !== range(0, count($arg) - 1)) {

if (array_key_exists(self::BLOCKS, $arg)) {
$blocks = $arg[self::BLOCKS];
}

unset($arg[self::BLOCKS]);

$quickDefinitions = $arg;

continue;
}

$constructorArgs = $arg;

continue;
}

throw new Exception(sprintf(
'Unable to parse arguments sent to %s::mock()', get_class($this)
));
}

$builder->addBlackListedMethods($blocks);

if ($constructorArgs !== null) {
$builder->addBlackListedMethod('__construct'); 
} else {
$builder->setMockOriginalDestructor(true);
}

if ($partialMethods !== null && $constructorArgs === null) {
$constructorArgs = [];
}

$config = $builder->getMockConfiguration();

$this->checkForNamedMockClashes($config);

$def = $this->getGenerator()->generate($config);

$className = $def->getClassName();
if (class_exists($className, $attemptAutoload = false)) {
$rfc = new ReflectionClass($className);
if (! $rfc->implementsInterface(LegacyMockInterface::class)) {
throw new RuntimeException(sprintf('Could not load mock %s, class already exists', $className));
}
}

$this->getLoader()->load($def);

$mock = $this->_getInstance($className, $constructorArgs);
$mock->mockery_init($this, $config->getTargetObject(), $config->isInstanceMock());

if ($quickDefinitions !== []) {
if ($mockeryConfiguration->getQuickDefinitions()->shouldBeCalledAtLeastOnce()) {
$mock->shouldReceive($quickDefinitions)->atLeast()->once();
} else {
$mock->shouldReceive($quickDefinitions)->byDefault();
}
}


if ($expectationClosure instanceof Closure) {

$expectationClosure($mock);
}

return $this->rememberMock($mock);
}






public function mockery_allocateOrder()
{
return ++$this->_allocatedOrder;
}






public function mockery_close()
{
foreach ($this->_mocks as $mock) {
$mock->mockery_teardown();
}

$this->_mocks = [];
}






public function mockery_getCurrentOrder()
{
return $this->_currentOrder;
}






public function mockery_getExpectationCount()
{
$count = 0;
foreach ($this->_mocks as $mock) {
$count += $mock->mockery_getExpectationCount();
}

return $count;
}






public function mockery_getGroups()
{
return $this->_groups;
}








public function mockery_setCurrentOrder($order)
{
return $this->_currentOrder = $order;
}









public function mockery_setGroup($group, $order)
{
$this->_groups[$group] = $order;
}






public function mockery_teardown()
{
try {
$this->mockery_verify();
} catch (PHPException $phpException) {
$this->mockery_close();

throw $phpException;
}
}






public function mockery_thrownExceptions()
{

$exceptions = [];

foreach ($this->_mocks as $mock) {
foreach ($mock->mockery_thrownExceptions() as $exception) {
$exceptions[] = $exception;
}
}

return $exceptions;
}









public function mockery_validateOrder($method, $order, LegacyMockInterface $mock)
{
if ($order < $this->_currentOrder) {
$exception = new InvalidOrderException(
sprintf(
'Method %s called out of order: expected order %d, was %d',
$method,
$order,
$this->_currentOrder
)
);

$exception->setMock($mock)
->setMethodName($method)
->setExpectedOrder($order)
->setActualOrder($this->_currentOrder);

throw $exception;
}

$this->mockery_setCurrentOrder($order);
}




public function mockery_verify()
{
foreach ($this->_mocks as $mock) {
$mock->mockery_verify();
}
}

/**
@template






*/
public function rememberMock(LegacyMockInterface $mock)
{
$class = get_class($mock);

if (! array_key_exists($class, $this->_mocks)) {
return $this->_mocks[$class] = $mock;
}





return $this->_mocks[] = $mock;
}








public function self()
{
$mocks = array_values($this->_mocks);
$index = count($mocks) - 1;
return $mocks[$index];
}

/**
@template
@template





*/
protected function _getInstance($mockName, $constructorArgs = null)
{
if ($constructorArgs !== null) {
return (new ReflectionClass($mockName))->newInstanceArgs($constructorArgs);
}

try {
$instance = $this->instantiator->instantiate($mockName);
} catch (PHPException $phpException) {

$internalMockName = $mockName . '_Internal';

if (! class_exists($internalMockName)) {
eval(sprintf(
'class %s extends %s { public function __construct() {} }',
$internalMockName,
$mockName
));
}

$instance = new $internalMockName();
}

return $instance;
}

protected function checkForNamedMockClashes($config)
{
$name = $config->getName();

if ($name === null) {
return;
}

$hash = $config->getHash();

if (array_key_exists($name, $this->_namedMocks) && $hash !== $this->_namedMocks[$name]) {
throw new Exception(
sprintf("The mock named '%s' has been already defined with a different mock configuration", $name)
);
}

$this->_namedMocks[$name] = $hash;
}
}
