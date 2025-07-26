<?php










namespace Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" interface is deprecated and will be removed in 3.0.', BinaryProvider::class);







interface BinaryProvider
{



public function canBeUsed(): bool;




public function getRootDir(): string;






public function getEmbeddedBinary(): string;









public function getExtraFiles(): array;
}
