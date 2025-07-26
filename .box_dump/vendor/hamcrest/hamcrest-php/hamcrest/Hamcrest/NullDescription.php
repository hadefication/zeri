<?php
namespace Hamcrest;








class NullDescription implements Description
{

public function appendText($text)
{
return $this;
}

public function appendDescriptionOf(SelfDescribing $value)
{
return $this;
}

public function appendValue($value)
{
return $this;
}

public function appendValueList($start, $separator, $end, $values)
{
return $this;
}

public function appendList($start, $separator, $end, $values)
{
return $this;
}

public function __toString()
{
return '';
}
}
