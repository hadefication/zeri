<?php









namespace Mockery\Generator;

use Mockery\Exception;
use Serializable;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function array_unique;
use function array_values;
use function class_alias;
use function class_exists;
use function explode;
use function get_class;
use function implode;
use function in_array;
use function interface_exists;
use function is_object;
use function md5;
use function preg_match;
use function serialize;
use function strpos;
use function strtolower;
use function trait_exists;





class MockConfiguration
{





protected $allMethods = [];






protected $blackListedMethods = [];

protected $constantsMap = [];






protected $instanceMock = false;






protected $mockOriginalDestructor = false;






protected $name;






protected $parameterOverrides = [];





protected $targetClass;




protected $targetClassName;




protected $targetInterfaceNames = [];






protected $targetInterfaces = [];






protected $targetObject;




protected $targetTraitNames = [];






protected $targetTraits = [];






protected $whiteListedMethods = [];











public function __construct(
array $targets = [],
array $blackListedMethods = [],
array $whiteListedMethods = [],
$name = null,
$instanceMock = false,
array $parameterOverrides = [],
$mockOriginalDestructor = false,
array $constantsMap = []
) {
$this->addTargets($targets);
$this->blackListedMethods = $blackListedMethods;
$this->whiteListedMethods = $whiteListedMethods;
$this->name = $name;
$this->instanceMock = $instanceMock;
$this->parameterOverrides = $parameterOverrides;
$this->mockOriginalDestructor = $mockOriginalDestructor;
$this->constantsMap = $constantsMap;
}






public function generateName()
{
$nameBuilder = new MockNameBuilder();

$targetObject = $this->getTargetObject();
if ($targetObject !== null) {
$className = get_class($targetObject);

$nameBuilder->addPart(strpos($className, '@') !== false ? md5($className) : $className);
}

$targetClass = $this->getTargetClass();
if ($targetClass instanceof TargetClassInterface) {
$className = $targetClass->getName();

$nameBuilder->addPart(strpos($className, '@') !== false ? md5($className) : $className);
}

foreach ($this->getTargetInterfaces() as $targetInterface) {
$nameBuilder->addPart($targetInterface->getName());
}

return $nameBuilder->build();
}




public function getBlackListedMethods()
{
return $this->blackListedMethods;
}




public function getConstantsMap()
{
return $this->constantsMap;
}








public function getHash()
{
$vars = [
'targetClassName' => $this->targetClassName,
'targetInterfaceNames' => $this->targetInterfaceNames,
'targetTraitNames' => $this->targetTraitNames,
'name' => $this->name,
'blackListedMethods' => $this->blackListedMethods,
'whiteListedMethod' => $this->whiteListedMethods,
'instanceMock' => $this->instanceMock,
'parameterOverrides' => $this->parameterOverrides,
'mockOriginalDestructor' => $this->mockOriginalDestructor,
];

return md5(serialize($vars));
}







public function getMethodsToMock()
{
$methods = $this->getAllMethods();

foreach ($methods as $key => $method) {
if ($method->isFinal()) {
unset($methods[$key]);
}
}




$whiteListedMethods = $this->getWhiteListedMethods();
if ($whiteListedMethods !== []) {
$whitelist = array_map('strtolower', $whiteListedMethods);

return array_filter($methods, static function ($method) use ($whitelist) {
if ($method->isAbstract()) {
return true;
}

return in_array(strtolower($method->getName()), $whitelist, true);
});
}




$blackListedMethods = $this->getBlackListedMethods();
if ($blackListedMethods !== []) {
$blacklist = array_map('strtolower', $blackListedMethods);

$methods = array_filter($methods, static function ($method) use ($blacklist) {
return ! in_array(strtolower($method->getName()), $blacklist, true);
});
}







$targetClass = $this->getTargetClass();

if (
$targetClass !== null
&& $targetClass->implementsInterface(Serializable::class)
&& $targetClass->hasInternalAncestor()
) {
$methods = array_filter($methods, static function ($method) {
return $method->getName() !== 'unserialize';
});
}

return array_values($methods);
}




public function getName()
{
return $this->name;
}




public function getNamespaceName()
{
$parts = explode('\\', $this->getName());
array_pop($parts);

if ($parts !== []) {
return implode('\\', $parts);
}

return '';
}




public function getParameterOverrides()
{
return $this->parameterOverrides;
}




public function getShortName()
{
$parts = explode('\\', $this->getName());
return array_pop($parts);
}




public function getTargetClass()
{
if ($this->targetClass) {
return $this->targetClass;
}

if (! $this->targetClassName) {
return null;
}

if (class_exists($this->targetClassName)) {
$alias = null;
if (strpos($this->targetClassName, '@') !== false) {
$alias = (new MockNameBuilder())
->addPart('anonymous_class')
->addPart(md5($this->targetClassName))
->build();
class_alias($this->targetClassName, $alias);
}

$dtc = DefinedTargetClass::factory($this->targetClassName, $alias);

if ($this->getTargetObject() === null && $dtc->isFinal()) {
throw new Exception(
'The class ' . $this->targetClassName . ' is marked final and its methods'
. ' cannot be replaced. Classes marked final can be passed in'
. ' to \Mockery::mock() as instantiated objects to create a'
. ' partial mock, but only if the mock is not subject to type'
. ' hinting checks.'
);
}

$this->targetClass = $dtc;
} else {
$this->targetClass = UndefinedTargetClass::factory($this->targetClassName);
}

return $this->targetClass;
}




public function getTargetClassName()
{
return $this->targetClassName;
}




public function getTargetInterfaces()
{
if ($this->targetInterfaces !== []) {
return $this->targetInterfaces;
}

foreach ($this->targetInterfaceNames as $targetInterface) {
if (! interface_exists($targetInterface)) {
$this->targetInterfaces[] = UndefinedTargetClass::factory($targetInterface);
continue;
}

$dtc = DefinedTargetClass::factory($targetInterface);
$extendedInterfaces = array_keys($dtc->getInterfaces());
$extendedInterfaces[] = $targetInterface;

$traversableFound = false;
$iteratorShiftedToFront = false;
foreach ($extendedInterfaces as $interface) {
if (! $traversableFound && preg_match('/^\\?Iterator(|Aggregate)$/i', $interface)) {
break;
}

if (preg_match('/^\\\\?IteratorAggregate$/i', $interface)) {
$this->targetInterfaces[] = DefinedTargetClass::factory('\\IteratorAggregate');
$iteratorShiftedToFront = true;

continue;
}

if (preg_match('/^\\\\?Iterator$/i', $interface)) {
$this->targetInterfaces[] = DefinedTargetClass::factory('\\Iterator');
$iteratorShiftedToFront = true;

continue;
}

if (preg_match('/^\\\\?Traversable$/i', $interface)) {
$traversableFound = true;
}
}

if ($traversableFound && ! $iteratorShiftedToFront) {
$this->targetInterfaces[] = DefinedTargetClass::factory('\\IteratorAggregate');
}




$isTraversable = preg_match('/^\\\\?Traversable$/i', $targetInterface);
if ($isTraversable === 0 || $isTraversable === false) {
$this->targetInterfaces[] = $dtc;
}
}

return $this->targetInterfaces = array_unique($this->targetInterfaces);
}




public function getTargetObject()
{
return $this->targetObject;
}




public function getTargetTraits()
{
if ($this->targetTraits !== []) {
return $this->targetTraits;
}

foreach ($this->targetTraitNames as $targetTrait) {
$this->targetTraits[] = DefinedTargetClass::factory($targetTrait);
}

$this->targetTraits = array_unique($this->targetTraits); 
return $this->targetTraits;
}




public function getWhiteListedMethods()
{
return $this->whiteListedMethods;
}




public function isInstanceMock()
{
return $this->instanceMock;
}




public function isMockOriginalDestructor()
{
return $this->mockOriginalDestructor;
}





public function rename($className)
{
$targets = [];

if ($this->targetClassName) {
$targets[] = $this->targetClassName;
}

if ($this->targetInterfaceNames) {
$targets = array_merge($targets, $this->targetInterfaceNames);
}

if ($this->targetTraitNames) {
$targets = array_merge($targets, $this->targetTraitNames);
}

if ($this->targetObject) {
$targets[] = $this->targetObject;
}

return new self(
$targets,
$this->blackListedMethods,
$this->whiteListedMethods,
$className,
$this->instanceMock,
$this->parameterOverrides,
$this->mockOriginalDestructor,
$this->constantsMap
);
}







public function requiresCallStaticTypeHintRemoval()
{
foreach ($this->getAllMethods() as $method) {
if ($method->getName() === '__callStatic') {
$params = $method->getParameters();

if (! array_key_exists(1, $params)) {
return false;
}

return ! $params[1]->isArray();
}
}

return false;
}







public function requiresCallTypeHintRemoval()
{
foreach ($this->getAllMethods() as $method) {
if ($method->getName() === '__call') {
$params = $method->getParameters();
return ! $params[1]->isArray();
}
}

return false;
}




protected function addTarget($target)
{
if (is_object($target)) {
$this->setTargetObject($target);
$this->setTargetClassName(get_class($target));
return;
}

if ($target[0] !== '\\') {
$target = '\\' . $target;
}

if (class_exists($target)) {
$this->setTargetClassName($target);
return;
}

if (interface_exists($target)) {
$this->addTargetInterfaceName($target);
return;
}

if (trait_exists($target)) {
$this->addTargetTraitName($target);
return;
}







if ($this->getTargetClassName()) {
$this->addTargetInterfaceName($target);
return;
}

$this->setTargetClassName($target);
}








protected function addTargetInterfaceName($targetInterface)
{
$this->targetInterfaceNames[] = $targetInterface;
}




protected function addTargets($interfaces)
{
foreach ($interfaces as $interface) {
$this->addTarget($interface);
}
}




protected function addTargetTraitName($targetTraitName)
{
$this->targetTraitNames[] = $targetTraitName;
}




protected function getAllMethods()
{
if ($this->allMethods) {
return $this->allMethods;
}

$classes = $this->getTargetInterfaces();

if ($this->getTargetClass()) {
$classes[] = $this->getTargetClass();
}

$methods = [];
foreach ($classes as $class) {
$methods = array_merge($methods, $class->getMethods());
}

foreach ($this->getTargetTraits() as $trait) {
foreach ($trait->getMethods() as $method) {
if ($method->isAbstract()) {
$methods[] = $method;
}
}
}

$names = [];
$methods = array_filter($methods, static function ($method) use (&$names) {
if (in_array($method->getName(), $names, true)) {
return false;
}

$names[] = $method->getName();
return true;
});

return $this->allMethods = $methods;
}




protected function setTargetClassName($targetClassName)
{
$this->targetClassName = $targetClassName;
}




protected function setTargetObject($object)
{
$this->targetObject = $object;
}
}
