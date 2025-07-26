<?php





namespace Whoops\Util;






class HtmlDumperOutput
{
private $output;

public function __invoke($line, $depth)
{

if ($depth >= 0) {

$this->output .= str_repeat('  ', $depth) . $line . "\n";
}
}

public function getOutput()
{
return $this->output;
}

public function clear()
{
$this->output = null;
}
}
