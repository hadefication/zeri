<?php

namespace Illuminate\Support\Traits;

use Illuminate\Container\Container;

trait Localizable
{







public function withLocale($locale, $callback)
{
if (! $locale) {
return $callback();
}

$app = Container::getInstance();

$original = $app->getLocale();

try {
$app->setLocale($locale);

return $callback();
} finally {
$app->setLocale($original);
}
}
}
