<?php
namespace Hamcrest\Core;





use Hamcrest\BaseMatcher;
use Hamcrest\Description;
use Hamcrest\Util;

abstract class ShortcutCombination extends BaseMatcher
{




private $_matchers;

public function __construct(array $matchers)
{
Util::checkAllAreMatchers($matchers);

$this->_matchers = $matchers;
}

protected function matchesWithShortcut($item, $shortcut)
{
/**
@@var $matcher \Hamcrest\Matcher */
foreach ($this->_matchers as $matcher) {
if ($matcher->matches($item) == $shortcut) {
return $shortcut;
}
}

return !$shortcut;
}

public function describeToWithOperator(Description $description, $operator)
{
$description->appendList('(', ' ' . $operator . ' ', ')', $this->_matchers);
}
}
