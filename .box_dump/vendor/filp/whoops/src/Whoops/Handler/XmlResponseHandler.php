<?php





namespace Whoops\Handler;

use SimpleXMLElement;
use Whoops\Exception\Formatter;






class XmlResponseHandler extends Handler
{



private $returnFrames = false;





public function addTraceToOutput($returnFrames = null)
{
if (func_num_args() == 0) {
return $this->returnFrames;
}

$this->returnFrames = (bool) $returnFrames;
return $this;
}




public function handle()
{
$response = [
'error' => Formatter::formatExceptionAsDataArray(
$this->getInspector(),
$this->addTraceToOutput(),
$this->getRun()->getFrameFilters()
),
];

echo self::toXml($response);

return Handler::QUIT;
}




public function contentType()
{
return 'application/xml';
}






private static function addDataToNode(\SimpleXMLElement $node, $data)
{
assert(is_array($data) || $data instanceof Traversable);

foreach ($data as $key => $value) {
if (is_numeric($key)) {

$key = "unknownNode_". (string) $key;
}


$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

if (is_array($value)) {
$child = $node->addChild($key);
self::addDataToNode($child, $value);
} else {
$value = str_replace('&', '&amp;', print_r($value, true));
$node->addChild($key, $value);
}
}

return $node;
}







private static function toXml($data)
{
assert(is_array($data) || $data instanceof Traversable);

$node = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><root />");

return self::addDataToNode($node, $data)->asXML();
}
}
