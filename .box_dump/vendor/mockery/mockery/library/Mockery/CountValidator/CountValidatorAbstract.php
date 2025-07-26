<?php









namespace Mockery\CountValidator;

use Mockery\Expectation;

abstract class CountValidatorAbstract implements CountValidatorInterface
{





protected $_expectation = null;






protected $_limit = null;






public function __construct(Expectation $expectation, $limit)
{
$this->_expectation = $expectation;
$this->_limit = $limit;
}








public function isEligible($n)
{
return $n < $this->_limit;
}








abstract public function validate($n);
}
