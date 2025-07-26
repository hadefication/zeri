<?php









namespace Mockery;

use Closure;
use Hamcrest\Matcher;
use Hamcrest_Matcher;
use InvalidArgumentException;
use LogicException;
use Mockery\Matcher\MatcherInterface;

use function array_key_exists;
use function array_merge;
use function class_implements;
use function get_parent_class;
use function is_a;
use function sprintf;
use function strtolower;
use function trigger_error;

use const E_USER_DEPRECATED;
use const PHP_MAJOR_VERSION;

class Configuration
{








protected $_allowMockingMethodsUnnecessarily = true;







protected $_allowMockingNonExistentMethod = true;








protected $_constantsMap = [];








protected $_defaultMatchers = [];








protected $_internalClassParamMap = [];








protected $_objectFormatters = [];




protected $_quickDefinitionsConfiguration;







protected $_reflectionCacheEnabled = true;

public function __construct()
{
$this->_quickDefinitionsConfiguration = new QuickDefinitionsConfiguration();
}










public function allowMockingMethodsUnnecessarily($flag = true)
{
@trigger_error(
sprintf('The %s method is deprecated and will be removed in a future version of Mockery', __METHOD__),
E_USER_DEPRECATED
);

$this->_allowMockingMethodsUnnecessarily = (bool) $flag;
}








public function allowMockingNonExistentMethods($flag = true)
{
$this->_allowMockingNonExistentMethod = (bool) $flag;
}











public function disableReflectionCache()
{
$this->_reflectionCacheEnabled = false;
}











public function enableReflectionCache()
{
$this->_reflectionCacheEnabled = true;
}






public function getConstantsMap()
{
return $this->_constantsMap;
}








public function getDefaultMatcher($class)
{
$classes = [];

$parentClass = $class;

do {
$classes[] = $parentClass;

$parentClass = get_parent_class($parentClass);
} while ($parentClass !== false);

$classesAndInterfaces = array_merge($classes, class_implements($class));

foreach ($classesAndInterfaces as $type) {
if (array_key_exists($type, $this->_defaultMatchers)) {
return $this->_defaultMatchers[$type];
}
}

return null;
}









public function getInternalClassMethodParamMap($class, $method)
{
$class = strtolower($class);
$method = strtolower($method);
if (! array_key_exists($class, $this->_internalClassParamMap)) {
return null;
}

if (! array_key_exists($method, $this->_internalClassParamMap[$class])) {
return null;
}

return $this->_internalClassParamMap[$class][$method];
}






public function getInternalClassMethodParamMaps()
{
return $this->_internalClassParamMap;
}









public function getObjectFormatter($class, $defaultFormatter)
{
$parentClass = $class;

do {
$classes[] = $parentClass;

$parentClass = get_parent_class($parentClass);
} while ($parentClass !== false);

$classesAndInterfaces = array_merge($classes, class_implements($class));

foreach ($classesAndInterfaces as $type) {
if (array_key_exists($type, $this->_objectFormatters)) {
return $this->_objectFormatters[$type];
}
}

return $defaultFormatter;
}




public function getQuickDefinitions(): QuickDefinitionsConfiguration
{
return $this->_quickDefinitionsConfiguration;
}








public function mockingMethodsUnnecessarilyAllowed()
{
@trigger_error(
sprintf('The %s method is deprecated and will be removed in a future version of Mockery', __METHOD__),
E_USER_DEPRECATED
);

return $this->_allowMockingMethodsUnnecessarily;
}






public function mockingNonExistentMethodsAllowed()
{
return $this->_allowMockingNonExistentMethod;
}






public function reflectionCacheEnabled()
{
return $this->_reflectionCacheEnabled;
}






public function resetInternalClassMethodParamMaps()
{
$this->_internalClassParamMap = [];
}










public function setConstantsMap(array $map)
{
$this->_constantsMap = $map;
}









public function setDefaultMatcher($class, $matcherClass)
{
$isHamcrest = is_a($matcherClass, Matcher::class, true)
|| is_a($matcherClass, Hamcrest_Matcher::class, true);

if ($isHamcrest) {
@trigger_error('Hamcrest package has been deprecated and will be removed in 2.0', E_USER_DEPRECATED);
}

if (! $isHamcrest && ! is_a($matcherClass, MatcherInterface::class, true)) {
throw new InvalidArgumentException(sprintf(
"Matcher class must implement %s, '%s' given.",
MatcherInterface::class,
$matcherClass
));
}

$this->_defaultMatchers[$class] = $matcherClass;
}












public function setInternalClassMethodParamMap($class, $method, array $map)
{
if (PHP_MAJOR_VERSION > 7) {
throw new LogicException(
'Internal class parameter overriding is not available in PHP 8. Incompatible signatures have been reclassified as fatal errors.'
);
}

$class = strtolower($class);

if (! array_key_exists($class, $this->_internalClassParamMap)) {
$this->_internalClassParamMap[$class] = [];
}

$this->_internalClassParamMap[$class][strtolower($method)] = $map;
}









public function setObjectFormatter($class, $formatterCallback)
{
$this->_objectFormatters[$class] = $formatterCallback;
}
}
