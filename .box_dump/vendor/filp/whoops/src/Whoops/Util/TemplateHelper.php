<?php





namespace Whoops\Util;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Cloner\AbstractCloner;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Whoops\Exception\Frame;




class TemplateHelper
{




private $variables = [];




private $htmlDumper;




private $htmlDumperOutput;




private $cloner;




private $applicationRootPath;

public function __construct()
{

$this->applicationRootPath = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
}







public function escape($raw)
{
$flags = ENT_QUOTES;



if (defined("ENT_SUBSTITUTE") && !defined("HHVM_VERSION")) {
$flags |= ENT_SUBSTITUTE;
} else {




$flags |= ENT_IGNORE;
}

$raw = str_replace(chr(9), '    ', $raw);

return htmlspecialchars($raw, $flags, "UTF-8");
}








public function escapeButPreserveUris($raw)
{
$escaped = $this->escape($raw);
return preg_replace(
"@([A-z]+?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@",
"<a href=\"$1\" target=\"_blank\" rel=\"noreferrer noopener\">$1</a>",
$escaped
);
}








public function breakOnDelimiter($delimiter, $s)
{
$parts = explode($delimiter, $s);
foreach ($parts as &$part) {
$part = '<span class="delimiter">' . $part . '</span>';
}

return implode($delimiter, $parts);
}







public function shorten($path)
{
if ($this->applicationRootPath != "/") {
$path = str_replace($this->applicationRootPath, '&hellip;', $path);
}

return $path;
}

private function getDumper()
{
if (!$this->htmlDumper && class_exists('Symfony\Component\VarDumper\Cloner\VarCloner')) {
$this->htmlDumperOutput = new HtmlDumperOutput();

$this->htmlDumper = new HtmlDumper($this->htmlDumperOutput);

$styles = [
'default' => 'color:#FFFFFF; line-height:normal; font:12px "Inconsolata", "Fira Mono", "Source Code Pro", Monaco, Consolas, "Lucida Console", monospace !important; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: normal',
'num' => 'color:#BCD42A',
'const' => 'color: #4bb1b1;',
'str' => 'color:#BCD42A',
'note' => 'color:#ef7c61',
'ref' => 'color:#A0A0A0',
'public' => 'color:#FFFFFF',
'protected' => 'color:#FFFFFF',
'private' => 'color:#FFFFFF',
'meta' => 'color:#FFFFFF',
'key' => 'color:#BCD42A',
'index' => 'color:#ef7c61',
];
$this->htmlDumper->setStyles($styles);
}

return $this->htmlDumper;
}







public function dump($value)
{
$dumper = $this->getDumper();

if ($dumper) {


if (class_exists('Symfony\Component\VarDumper\Caster\Caster')) {
$cloneVar = $this->getCloner()->cloneVar($value, Caster::EXCLUDE_VERBOSE);

} else {
$cloneVar = $this->getCloner()->cloneVar($value);
}

$dumper->dump(
$cloneVar,
$this->htmlDumperOutput
);

$output = $this->htmlDumperOutput->getOutput();
$this->htmlDumperOutput->clear();

return $output;
}

return htmlspecialchars(print_r($value, true));
}







public function dumpArgs(Frame $frame)
{

if (!$this->getDumper()) {
return '';
}

$html = '';
$numFrames = count($frame->getArgs());

if ($numFrames > 0) {
$html = '<ol class="linenums">';
foreach ($frame->getArgs() as $j => $frameArg) {
$html .= '<li>'. $this->dump($frameArg) .'</li>';
}
$html .= '</ol>';
}

return $html;
}







public function slug($original)
{
$slug = str_replace(" ", "-", $original);
$slug = preg_replace('/[^\w\d\-\_]/i', '', $slug);
return strtolower($slug);
}








public function render($template, ?array $additionalVariables = null)
{
$variables = $this->getVariables();


$variables["tpl"] = $this;

if ($additionalVariables !== null) {
$variables = array_replace($variables, $additionalVariables);
}

call_user_func(function () {
extract(func_get_arg(1));
require func_get_arg(0);
}, $template, $variables);
}





public function setVariables(array $variables)
{
$this->variables = $variables;
}







public function setVariable($variableName, $variableValue)
{
$this->variables[$variableName] = $variableValue;
}









public function getVariable($variableName, $defaultValue = null)
{
return isset($this->variables[$variableName]) ?
$this->variables[$variableName] : $defaultValue;
}






public function delVariable($variableName)
{
unset($this->variables[$variableName]);
}






public function getVariables()
{
return $this->variables;
}






public function setCloner($cloner)
{
$this->cloner = $cloner;
}






public function getCloner()
{
if (!$this->cloner) {
$this->cloner = new VarCloner();
}
return $this->cloner;
}






public function setApplicationRootPath($applicationRootPath)
{
$this->applicationRootPath = $applicationRootPath;
}






public function getApplicationRootPath()
{
return $this->applicationRootPath;
}
}
