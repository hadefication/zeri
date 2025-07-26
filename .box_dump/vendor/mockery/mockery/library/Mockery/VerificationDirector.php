<?php









namespace Mockery;

class VerificationDirector
{



private $expectation;




private $receivedMethodCalls;

public function __construct(ReceivedMethodCalls $receivedMethodCalls, VerificationExpectation $expectation)
{
$this->receivedMethodCalls = $receivedMethodCalls;
$this->expectation = $expectation;
}




public function atLeast()
{
return $this->cloneWithoutCountValidatorsApplyAndVerify('atLeast', []);
}




public function atMost()
{
return $this->cloneWithoutCountValidatorsApplyAndVerify('atMost', []);
}







public function between($minimum, $maximum)
{
return $this->cloneWithoutCountValidatorsApplyAndVerify('between', [$minimum, $maximum]);
}




public function once()
{
return $this->cloneWithoutCountValidatorsApplyAndVerify('once', []);
}






public function times($limit = null)
{
return $this->cloneWithoutCountValidatorsApplyAndVerify('times', [$limit]);
}




public function twice()
{
return $this->cloneWithoutCountValidatorsApplyAndVerify('twice', []);
}

public function verify()
{
$this->receivedMethodCalls->verify($this->expectation);
}

/**
@template




*/
public function with(...$args)
{
return $this->cloneApplyAndVerify('with', $args);
}




public function withAnyArgs()
{
return $this->cloneApplyAndVerify('withAnyArgs', []);
}

/**
@template




*/
public function withArgs($args)
{
return $this->cloneApplyAndVerify('withArgs', [$args]);
}




public function withNoArgs()
{
return $this->cloneApplyAndVerify('withNoArgs', []);
}







protected function cloneApplyAndVerify($method, $args)
{
$verificationExpectation = clone $this->expectation;

$verificationExpectation->{$method}(...$args);

$verificationDirector = new self($this->receivedMethodCalls, $verificationExpectation);

$verificationDirector->verify();

return $verificationDirector;
}







protected function cloneWithoutCountValidatorsApplyAndVerify($method, $args)
{
$verificationExpectation = clone $this->expectation;

$verificationExpectation->clearCountValidators();

$verificationExpectation->{$method}(...$args);

$verificationDirector = new self($this->receivedMethodCalls, $verificationExpectation);

$verificationDirector->verify();

return $verificationDirector;
}
}
