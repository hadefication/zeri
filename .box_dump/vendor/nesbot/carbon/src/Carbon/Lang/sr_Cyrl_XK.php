<?php










use Symfony\Component\Translation\PluralizationRules;


if (class_exists(PluralizationRules::class)) {
PluralizationRules::set(static function ($number) {
return PluralizationRules::get($number, 'sr');
}, 'sr_Cyrl_XK');
}


return array_replace_recursive(require __DIR__.'/sr_Cyrl_BA.php', [
'weekdays' => ['недеља', 'понедељак', 'уторак', 'среда', 'четвртак', 'петак', 'субота'],
]);
