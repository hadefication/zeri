<?php









namespace Mockery\CountValidator;

use Mockery\Exception\InvalidCountException;

use const PHP_EOL;

class Exact extends CountValidatorAbstract
{








public function validate($n)
{
if ($this->_limit !== $n) {
$because = $this->_expectation->getExceptionMessage();

$exception = new InvalidCountException(
'Method ' . (string) $this->_expectation
. ' from ' . $this->_expectation->getMock()->mockery_getName()
. ' should be called' . PHP_EOL
. ' exactly ' . $this->_limit . ' times but called ' . $n
. ' times.'
. ($because ? ' Because ' . $this->_expectation->getExceptionMessage() : '')
);
$exception->setMock($this->_expectation->getMock())
->setMethodName((string) $this->_expectation)
->setExpectedCountComparative('=')
->setExpectedCount($this->_limit)
->setActualCount($n);
throw $exception;
}
}
}
