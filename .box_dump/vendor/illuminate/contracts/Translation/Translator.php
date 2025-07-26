<?php

namespace Illuminate\Contracts\Translation;

interface Translator
{








public function get($key, array $replace = [], $locale = null);










public function choice($key, $number, array $replace = [], $locale = null);






public function getLocale();







public function setLocale($locale);
}
