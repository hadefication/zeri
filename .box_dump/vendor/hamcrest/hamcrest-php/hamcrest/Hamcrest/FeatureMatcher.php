<?php
namespace Hamcrest;










abstract class FeatureMatcher extends TypeSafeDiagnosingMatcher
{

private $_subMatcher;
private $_featureDescription;
private $_featureName;










public function __construct($type, $subtype, Matcher $subMatcher, $featureDescription, $featureName)
{
parent::__construct($type, $subtype);

$this->_subMatcher = $subMatcher;
$this->_featureDescription = $featureDescription;
$this->_featureName = $featureName;
}








abstract protected function featureValueOf($actual);

public function matchesSafelyWithDiagnosticDescription($actual, Description $mismatchDescription)
{
$featureValue = $this->featureValueOf($actual);

if (!$this->_subMatcher->matches($featureValue)) {
$mismatchDescription->appendText($this->_featureName)
->appendText(' was ')->appendValue($featureValue);

return false;
}

return true;
}

final public function describeTo(Description $description)
{
$description->appendText($this->_featureDescription)->appendText(' ')
->appendDescriptionOf($this->_subMatcher)
;
}
}
