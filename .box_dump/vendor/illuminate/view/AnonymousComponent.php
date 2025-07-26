<?php

namespace Illuminate\View;

class AnonymousComponent extends Component
{





protected $view;






protected $data = [];







public function __construct($view, $data)
{
$this->view = $view;
$this->data = $data;
}






public function render()
{
return $this->view;
}






public function data()
{
$this->attributes = $this->attributes ?: $this->newAttributeBag();

return array_merge(
($this->data['attributes'] ?? null)?->getAttributes() ?: [],
$this->attributes->getAttributes(),
$this->data,
['attributes' => $this->attributes]
);
}
}
