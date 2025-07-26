<?php









namespace Mockery\Generator;

interface TargetClassInterface
{







public static function factory($name);






public function getAttributes();






public function getInterfaces();






public function getMethods();






public function getName();






public function getNamespaceName();






public function getShortName();







public function hasInternalAncestor();









public function implementsInterface($interface);






public function inNamespace();






public function isAbstract();






public function isFinal();
}
