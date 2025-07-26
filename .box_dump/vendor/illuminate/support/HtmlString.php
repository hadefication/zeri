<?php

namespace Illuminate\Support;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;

class HtmlString implements Htmlable, Stringable
{





protected $html;






public function __construct($html = '')
{
$this->html = $html;
}






public function toHtml()
{
return $this->html;
}






public function isEmpty()
{
return ($this->html ?? '') === '';
}






public function isNotEmpty()
{
return ! $this->isEmpty();
}






public function __toString()
{
return $this->toHtml() ?? '';
}
}
