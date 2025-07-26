<?php

declare(strict_types=1);










namespace Carbon;

use ReflectionMethod;
use Symfony\Component\Translation;
use Symfony\Contracts\Translation\TranslatorInterface;

$transMethod = new ReflectionMethod(
class_exists(TranslatorInterface::class)
? TranslatorInterface::class
: Translation\Translator::class,
'trans',
);

require $transMethod->hasReturnType()
? __DIR__.'/../../lazy/Carbon/TranslatorStrongType.php'
: __DIR__.'/../../lazy/Carbon/TranslatorWeakType.php';

class Translator extends LazyTranslator
{

}
