<?php









namespace Mockery;

class VerificationExpectation extends Expectation
{
public function __clone()
{
parent::__clone();

$this->_actualCount = 0;
}




public function clearCountValidators()
{
$this->_countValidators = [];
}
}
