<?php










namespace Joli\JoliNotif\Driver;







interface BinaryProviderInterface
{



public function canBeUsed(): bool;




public function getRootDir(): string;






public function getEmbeddedBinary(): string;









public function getExtraFiles(): array;
}
