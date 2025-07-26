<?php










namespace Symfony\Component\Console\Helper;






interface HelperInterface
{



public function setHelperSet(?HelperSet $helperSet): void;




public function getHelperSet(): ?HelperSet;




public function getName(): string;
}
