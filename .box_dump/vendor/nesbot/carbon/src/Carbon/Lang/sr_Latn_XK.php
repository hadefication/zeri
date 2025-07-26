<?php










use Symfony\Component\Translation\PluralizationRules;


if (class_exists(PluralizationRules::class)) {
PluralizationRules::set(static function ($number) {
return PluralizationRules::get($number, 'sr');
}, 'sr_Latn_XK');
}


return array_replace_recursive(require __DIR__.'/sr_Latn_BA.php', [
'weekdays' => ['nedelja', 'ponedeljak', 'utorak', 'sreda', 'četvrtak', 'petak', 'subota'],
]);
