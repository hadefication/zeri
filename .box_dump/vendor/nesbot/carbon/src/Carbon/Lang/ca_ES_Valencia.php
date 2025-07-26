<?php










use Symfony\Component\Translation\PluralizationRules;


if (class_exists(PluralizationRules::class)) {
PluralizationRules::set(static function ($number) {
return PluralizationRules::get($number, 'ca');
}, 'ca_ES_Valencia');
}


return array_replace_recursive(require __DIR__.'/ca.php', [
]);
