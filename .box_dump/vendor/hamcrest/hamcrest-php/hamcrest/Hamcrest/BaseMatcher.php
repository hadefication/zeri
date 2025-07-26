<?php
namespace Hamcrest;










abstract class BaseMatcher implements Matcher
{

public function describeMismatch($item, Description $description)
{
$description->appendText('was ')->appendValue($item);
}

public function __toString()
{
return StringDescription::toString($this);
}

public function __invoke()
{
return call_user_func_array(array($this, 'matches'), func_get_args());
}
}
