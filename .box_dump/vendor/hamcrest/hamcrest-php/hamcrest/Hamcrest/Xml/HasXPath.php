<?php
namespace Hamcrest\Xml;




use Hamcrest\Core\IsEqual;
use Hamcrest\Description;
use Hamcrest\DiagnosingMatcher;
use Hamcrest\Matcher;






class HasXPath extends DiagnosingMatcher
{






private $_xpath;







private $_matcher;

public function __construct($xpath, ?Matcher $matcher = null)
{
$this->_xpath = $xpath;
$this->_matcher = $matcher;
}








protected function matchesWithDiagnosticDescription($actual, Description $mismatchDescription)
{
if (is_string($actual)) {
$actual = $this->createDocument($actual);
} elseif (!$actual instanceof \DOMNode) {
$mismatchDescription->appendText('was ')->appendValue($actual);

return false;
}
$result = $this->evaluate($actual);
if ($result instanceof \DOMNodeList) {
return $this->matchesContent($result, $mismatchDescription);
} else {
return $this->matchesExpression($result, $mismatchDescription);
}
}









protected function createDocument($text)
{
$document = new \DOMDocument();
if (preg_match('/^\s*<\?xml/', $text)) {
if (!@$document->loadXML($text)) {
throw new \InvalidArgumentException('Must pass a valid XML document');
}
} else {
if (!@$document->loadHTML($text)) {
throw new \InvalidArgumentException('Must pass a valid HTML or XHTML document');
}
}

return $document;
}








protected function evaluate(\DOMNode $node)
{
if ($node instanceof \DOMDocument) {
$xpathDocument = new \DOMXPath($node);

return $xpathDocument->evaluate($this->_xpath);
} else {
$xpathDocument = new \DOMXPath($node->ownerDocument);

return $xpathDocument->evaluate($this->_xpath, $node);
}
}









protected function matchesContent(\DOMNodeList $nodes, Description $mismatchDescription)
{
if ($nodes->length == 0) {
$mismatchDescription->appendText('XPath returned no results');
} elseif ($this->_matcher === null) {
return true;
} else {
foreach ($nodes as $node) {
if ($this->_matcher->matches($node->textContent)) {
return true;
}
}
$content = array();
foreach ($nodes as $node) {
$content[] = $node->textContent;
}
$mismatchDescription->appendText('XPath returned ')
->appendValue($content);
}

return false;
}









protected function matchesExpression($result, Description $mismatchDescription)
{
if ($this->_matcher === null) {
if ($result) {
return true;
}
$mismatchDescription->appendText('XPath expression result was ')
->appendValue($result);
} else {
if ($this->_matcher->matches($result)) {
return true;
}
$mismatchDescription->appendText('XPath expression result ');
$this->_matcher->describeMismatch($result, $mismatchDescription);
}

return false;
}

public function describeTo(Description $description)
{
$description->appendText('XML or HTML document with XPath "')
->appendText($this->_xpath)
->appendText('"');
if ($this->_matcher !== null) {
$description->appendText(' ');
$this->_matcher->describeTo($description);
}
}

/**
@factory




*/
public static function hasXPath($xpath, $matcher = null)
{
if ($matcher === null || $matcher instanceof Matcher) {
return new self($xpath, $matcher);
} elseif (is_int($matcher) && strpos($xpath, 'count(') !== 0) {
$xpath = 'count(' . $xpath . ')';
}

return new self($xpath, IsEqual::equalTo($matcher));
}
}
