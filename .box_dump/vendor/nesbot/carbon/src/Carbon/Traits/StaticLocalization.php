<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\FactoryImmutable;
use Symfony\Contracts\Translation\TranslatorInterface;




trait StaticLocalization
{





public static function setHumanDiffOptions(int $humanDiffOptions): void
{
FactoryImmutable::getDefaultInstance()->setHumanDiffOptions($humanDiffOptions);
}






public static function enableHumanDiffOption(int $humanDiffOption): void
{
FactoryImmutable::getDefaultInstance()->enableHumanDiffOption($humanDiffOption);
}






public static function disableHumanDiffOption(int $humanDiffOption): void
{
FactoryImmutable::getDefaultInstance()->disableHumanDiffOption($humanDiffOption);
}




public static function getHumanDiffOptions(): int
{
return FactoryImmutable::getInstance()->getHumanDiffOptions();
}








public static function setTranslator(TranslatorInterface $translator): void
{
FactoryImmutable::getDefaultInstance()->setTranslator($translator);
}




public static function getTranslator(): TranslatorInterface
{
return FactoryImmutable::getInstance()->getTranslator();
}
}
