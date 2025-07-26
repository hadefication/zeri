<?php
namespace Hamcrest;











interface Description
{








public function appendText($text);









public function appendDescriptionOf(SelfDescribing $value);








public function appendValue($value);











public function appendValueList($start, $separator, $end, $values);













public function appendList($start, $separator, $end, $values);
}
