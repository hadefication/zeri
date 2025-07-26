<?php
namespace Hamcrest;








abstract class DiagnosingMatcher extends BaseMatcher
{

final public function matches($item)
{
return $this->matchesWithDiagnosticDescription($item, new NullDescription());
}

public function describeMismatch($item, Description $mismatchDescription)
{
$this->matchesWithDiagnosticDescription($item, $mismatchDescription);
}

abstract protected function matchesWithDiagnosticDescription($item, Description $mismatchDescription);
}
