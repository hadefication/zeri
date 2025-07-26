<?php
namespace Hamcrest;






abstract class TypeSafeDiagnosingMatcher extends TypeSafeMatcher
{

final public function matchesSafely($item)
{
return $this->matchesSafelyWithDiagnosticDescription($item, new NullDescription());
}

final public function describeMismatchSafely($item, Description $mismatchDescription)
{
$this->matchesSafelyWithDiagnosticDescription($item, $mismatchDescription);
}







abstract protected function matchesSafelyWithDiagnosticDescription($item, Description $mismatchDescription);
}
