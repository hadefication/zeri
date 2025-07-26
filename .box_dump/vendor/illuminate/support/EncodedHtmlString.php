<?php

namespace Illuminate\Support;

use BackedEnum;
use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Contracts\Support\Htmlable;

class EncodedHtmlString extends HtmlString
{





protected $html;






protected static $encodeUsingFactory;







public function __construct($html = '', protected bool $doubleEncode = true)
{
parent::__construct($html);
}











public static function convert($value, bool $withQuote = true, bool $doubleEncode = true)
{
$flag = $withQuote ? ENT_QUOTES : ENT_NOQUOTES;

return htmlspecialchars($value ?? '', $flag | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
}






#[\Override]
public function toHtml()
{
$value = $this->html;

if ($value instanceof DeferringDisplayableValue) {
$value = $value->resolveDisplayableValue();
}

if ($value instanceof Htmlable) {
return $value->toHtml();
}

if ($value instanceof BackedEnum) {
$value = $value->value;
}

return (static::$encodeUsingFactory ?? function ($value, $doubleEncode) {
return static::convert($value, doubleEncode: $doubleEncode);
})($value, $this->doubleEncode);
}







public static function encodeUsing(?callable $factory = null)
{
static::$encodeUsingFactory = $factory;
}






public static function flushState()
{
static::$encodeUsingFactory = null;
}
}
