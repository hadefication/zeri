<?php











declare(strict_types=1);

namespace Ramsey\Collection\Tool;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;
use Ramsey\Collection\Exception\UnsupportedOperationException;
use ReflectionProperty;

use function is_array;
use function is_object;
use function method_exists;
use function property_exists;
use function sprintf;




trait ValueExtractorTrait
{



abstract public function getType(): string;

















protected function extractValue(mixed $element, ?string $propertyOrMethod): mixed
{
if ($propertyOrMethod === null) {
return $element;
}

if (!is_object($element) && !is_array($element)) {
throw new UnsupportedOperationException(sprintf(
'The collection type "%s" does not support the $propertyOrMethod parameter',
$this->getType(),
));
}

if (is_array($element)) {
return $element[$propertyOrMethod] ?? throw new InvalidPropertyOrMethod(sprintf(
'Key or index "%s" not found in collection elements',
$propertyOrMethod,
));
}

if (property_exists($element, $propertyOrMethod) && method_exists($element, $propertyOrMethod)) {
$reflectionProperty = new ReflectionProperty($element, $propertyOrMethod);
if ($reflectionProperty->isPublic()) {
return $element->$propertyOrMethod;
}

return $element->{$propertyOrMethod}();
}

if (property_exists($element, $propertyOrMethod)) {
return $element->$propertyOrMethod;
}

if (method_exists($element, $propertyOrMethod)) {
return $element->{$propertyOrMethod}();
}

if (isset($element->$propertyOrMethod)) {
return $element->$propertyOrMethod;
}

throw new InvalidPropertyOrMethod(sprintf(
'Method or property "%s" not defined in %s',
$propertyOrMethod,
$element::class,
));
}
}
