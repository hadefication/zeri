<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\Portuguese;

use Doctrine\Inflector\Rules\Pattern;

final class Uninflected
{

public static function getSingular(): iterable
{
yield from self::getDefault();
}


public static function getPlural(): iterable
{
yield from self::getDefault();
}


private static function getDefault(): iterable
{
yield new Pattern('tórax');
yield new Pattern('tênis');
yield new Pattern('ônibus');
yield new Pattern('lápis');
yield new Pattern('fênix');
}
}
