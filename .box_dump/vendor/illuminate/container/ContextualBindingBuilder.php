<?php

namespace Illuminate\Container;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualBindingBuilder as ContextualBindingBuilderContract;

class ContextualBindingBuilder implements ContextualBindingBuilderContract
{





protected $container;






protected $concrete;






protected $needs;







public function __construct(Container $container, $concrete)
{
$this->concrete = $concrete;
$this->container = $container;
}







public function needs($abstract)
{
$this->needs = $abstract;

return $this;
}







public function give($implementation)
{
foreach (Util::arrayWrap($this->concrete) as $concrete) {
$this->container->addContextualBinding($concrete, $this->needs, $implementation);
}
}







public function giveTagged($tag)
{
$this->give(function ($container) use ($tag) {
$taggedServices = $container->tagged($tag);

return is_array($taggedServices) ? $taggedServices : iterator_to_array($taggedServices);
});
}








public function giveConfig($key, $default = null)
{
$this->give(fn ($container) => $container->get('config')->get($key, $default));
}
}
