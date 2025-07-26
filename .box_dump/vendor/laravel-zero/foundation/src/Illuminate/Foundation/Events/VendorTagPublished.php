<?php

namespace Illuminate\Foundation\Events;

class VendorTagPublished
{





public $tag;






public $paths;







public function __construct($tag, $paths)
{
$this->tag = $tag;
$this->paths = $paths;
}
}
