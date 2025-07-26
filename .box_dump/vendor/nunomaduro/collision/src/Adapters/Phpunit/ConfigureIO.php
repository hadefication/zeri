<?php

declare(strict_types=1);










namespace NunoMaduro\Collision\Adapters\Phpunit;

use ReflectionObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;




final class ConfigureIO
{






public static function of(InputInterface $input, Output $output): void
{
$application = new Application;
$reflector = new ReflectionObject($application);
$method = $reflector->getMethod('configureIO');
$method->setAccessible(true);
$method->invoke($application, $input, $output);
}
}
