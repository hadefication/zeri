<?php









namespace Mockery\Matcher;





abstract class MatcherAbstract implements MatcherInterface
{
/**
@template




*/
protected $_expected = null;

/**
@template




*/
public function __construct($expected = null)
{
$this->_expected = $expected;
}
}
