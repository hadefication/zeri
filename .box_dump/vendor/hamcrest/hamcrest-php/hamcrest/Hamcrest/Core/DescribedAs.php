<?php
namespace Hamcrest\Core;




use Hamcrest\BaseMatcher;
use Hamcrest\Description;
use Hamcrest\Matcher;




class DescribedAs extends BaseMatcher
{

private $_descriptionTemplate;
private $_matcher;
private $_values;

const ARG_PATTERN = '/%([0-9]+)/';

public function __construct($descriptionTemplate, Matcher $matcher, array $values)
{
$this->_descriptionTemplate = $descriptionTemplate;
$this->_matcher = $matcher;
$this->_values = $values;
}

public function matches($item)
{
return $this->_matcher->matches($item);
}

public function describeTo(Description $description)
{
$textStart = 0;
while (preg_match(self::ARG_PATTERN, $this->_descriptionTemplate, $matches, PREG_OFFSET_CAPTURE, $textStart)) {
$text = $matches[0][0];
$index = $matches[1][0];
$offset = $matches[0][1];

$description->appendText(substr($this->_descriptionTemplate, $textStart, $offset - $textStart));
$description->appendValue($this->_values[$index]);

$textStart = $offset + strlen($text);
}

if ($textStart < strlen($this->_descriptionTemplate)) {
$description->appendText(substr($this->_descriptionTemplate, $textStart));
}
}

/**
@factory


*/
public static function describedAs()
{
$args = func_get_args();
$description = array_shift($args);
$matcher = array_shift($args);
$values = $args;

return new self($description, $matcher, $values);
}
}
