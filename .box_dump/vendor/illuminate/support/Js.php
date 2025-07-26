<?php

namespace Illuminate\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Stringable;
use UnitEnum;

class Js implements Htmlable, Stringable
{





protected $js;






protected const REQUIRED_FLAGS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR;










public function __construct($data, $flags = 0, $depth = 512)
{
$this->js = $this->convertDataToJavaScriptExpression($data, $flags, $depth);
}











public static function from($data, $flags = 0, $depth = 512)
{
return new static($data, $flags, $depth);
}











protected function convertDataToJavaScriptExpression($data, $flags = 0, $depth = 512)
{
if ($data instanceof self) {
return $data->toHtml();
}

if ($data instanceof Htmlable &&
! $data instanceof Arrayable &&
! $data instanceof Jsonable &&
! $data instanceof JsonSerializable) {
$data = $data->toHtml();
}

if ($data instanceof UnitEnum) {
$data = enum_value($data);
}

$json = static::encode($data, $flags, $depth);

if (is_string($data)) {
return "'".substr($json, 1, -1)."'";
}

return $this->convertJsonToJavaScriptExpression($json, $flags);
}











public static function encode($data, $flags = 0, $depth = 512)
{
if ($data instanceof Jsonable) {
return $data->toJson($flags | static::REQUIRED_FLAGS);
}

if ($data instanceof Arrayable && ! ($data instanceof JsonSerializable)) {
$data = $data->toArray();
}

return json_encode($data, $flags | static::REQUIRED_FLAGS, $depth);
}










protected function convertJsonToJavaScriptExpression($json, $flags = 0)
{
if ($json === '[]' || $json === '{}') {
return $json;
}

if (Str::startsWith($json, ['"', '{', '['])) {
return "JSON.parse('".substr(json_encode($json, $flags | static::REQUIRED_FLAGS), 1, -1)."')";
}

return $json;
}






public function toHtml()
{
return $this->js;
}






public function __toString()
{
return $this->toHtml();
}
}
