<?php





class FactoryParameter
{



private $method;




private $reflector;

public function __construct(FactoryMethod $method, ReflectionParameter $reflector)
{
$this->method = $method;
$this->reflector = $reflector;
}






public function getDeclaration()
{
$code = $this->getTypeCode() . $this->getInvocation();

if ($this->reflector->isOptional()) {
$default = $this->reflector->getDefaultValue();
if (is_null($default)) {
$default = 'null';
} elseif (is_bool($default)) {
$default = $default ? 'true' : 'false';
} elseif (is_string($default)) {
$default = "'" . $default . "'";
} elseif (is_numeric($default)) {
$default = strval($default);
} elseif (is_array($default)) {
$default = 'array()';
} else {
echo 'Warning: unknown default type for ' . $this->getMethod()->getFullName() . "\n";
var_dump($default);
$default = 'null';
}
$code .= ' = ' . $default;
}
return $code;
}






private function getTypeCode()
{

if (PHP_VERSION_ID < 70000) {
if ($this->reflector->isArray()) {
return 'array';
}

$class = $this->reflector->getClass();

return $class ? sprintf('\\%s ', $class->getName()) : '';
}

if (!$this->reflector->hasType()) {
return '';
}

$type = $this->reflector->getType();
$name = self::getQualifiedName($type);


return (PHP_VERSION_ID >= 70100 && $type->allowsNull()) ? sprintf('?%s ', $name) : sprintf('%s ', $name);
}











private static function getQualifiedName(ReflectionType $type)
{

if ($type instanceof ReflectionUnionType) {
return implode('|', array_map(function (ReflectionType $type) {


return self::getQualifiedName($type);
}, $type->getTypes()));
}


$name = $type instanceof ReflectionNamedType ? $type->getName() : (string) $type;

return $type->isBuiltin() ? $name : sprintf('\\%s', $name);
}






public function getInvocation()
{
return sprintf('$%s', $this->reflector->getName());
}






public function getMethod()
{
return $this->method;
}
}
