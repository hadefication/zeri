<?php
namespace Hamcrest\Core;




use Hamcrest\Description;
use Hamcrest\FeatureMatcher;
use Hamcrest\Matcher;
use Hamcrest\Util;




class HasToString extends FeatureMatcher
{

public function __construct(Matcher $toStringMatcher)
{
parent::__construct(
self::TYPE_OBJECT,
null,
$toStringMatcher,
'an object with toString()',
'toString()'
);
}

public function matchesSafelyWithDiagnosticDescription($actual, Description $mismatchDescription)
{
if (method_exists($actual, 'toString') || method_exists($actual, '__toString')) {
return parent::matchesSafelyWithDiagnosticDescription($actual, $mismatchDescription);
}

return false;
}

protected function featureValueOf($actual)
{
if (method_exists($actual, 'toString')) {
return $actual->toString();
}

return (string) $actual;
}

/**
@factory


*/
public static function hasToString($matcher)
{
return new self(Util::wrapValueWithIsEqual($matcher));
}
}
