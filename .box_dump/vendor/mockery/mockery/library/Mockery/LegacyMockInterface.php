<?php









namespace Mockery;

use Closure;
use Throwable;

interface LegacyMockInterface
{







public function byDefault();






public function makePartial();






public function mockery_allocateOrder();

/**
@template







*/
public function mockery_findExpectation($method, array $args);






public function mockery_getContainer();






public function mockery_getCurrentOrder();






public function mockery_getExpectationCount();








public function mockery_getExpectationsFor($method);






public function mockery_getGroups();




public function mockery_getMockableMethods();




public function mockery_getMockableProperties();






public function mockery_getName();








public function mockery_init(?Container $container = null, $partialObject = null);




public function mockery_isAnonymous();








public function mockery_setCurrentOrder($order);








public function mockery_setExpectationsFor($method, ExpectationDirector $director);









public function mockery_setGroup($group, $order);






public function mockery_teardown();











public function mockery_validateOrder($method, $order);








public function mockery_verify();







public function shouldAllowMockingMethod($method);




public function shouldAllowMockingProtectedMethods();








public function shouldDeferMissing();




public function shouldHaveBeenCalled();

/**
@template




*/
public function shouldHaveReceived($method, $args = null);

/**
@template






*/
public function shouldIgnoreMissing($returnValue = null);

/**
@template



*/
public function shouldNotHaveBeenCalled(?array $args = null);

/**
@template




*/
public function shouldNotHaveReceived($method, $args = null);








public function shouldNotReceive(...$methodNames);








public function shouldReceive(...$methodNames);
}
