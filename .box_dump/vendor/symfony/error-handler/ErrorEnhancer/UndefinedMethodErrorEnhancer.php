<?php










namespace Symfony\Component\ErrorHandler\ErrorEnhancer;

use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;




class UndefinedMethodErrorEnhancer implements ErrorEnhancerInterface
{
public function enhance(\Throwable $error): ?\Throwable
{
if ($error instanceof FatalError) {
return null;
}

$message = $error->getMessage();
preg_match('/^Call to undefined method (.*)::(.*)\(\)$/', $message, $matches);
if (!$matches) {
return null;
}

$className = $matches[1];
$methodName = $matches[2];

$message = \sprintf('Attempted to call an undefined method named "%s" of class "%s".', $methodName, $className);

if ('' === $methodName || !class_exists($className) || null === $methods = get_class_methods($className)) {

return new UndefinedMethodError($message, $error);
}

$candidates = [];
foreach ($methods as $definedMethodName) {
$lev = levenshtein($methodName, $definedMethodName);
if ($lev <= \strlen($methodName) / 3 || str_contains($definedMethodName, $methodName)) {
$candidates[] = $definedMethodName;
}
}

if ($candidates) {
sort($candidates);
$last = array_pop($candidates).'"?';
if ($candidates) {
$candidates = 'e.g. "'.implode('", "', $candidates).'" or "'.$last;
} else {
$candidates = '"'.$last;
}

$message .= "\nDid you mean to call ".$candidates;
}

return new UndefinedMethodError($message, $error);
}
}
