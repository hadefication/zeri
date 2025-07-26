<?php










namespace Joli\JoliNotif\tests\Notifier;




trait BinaryProviderTestTrait
{
public function testRootDirectoryExists()
{
$notifier = $this->getNotifier();

$this->assertDirectoryExists($notifier->getRootDir());
}

public function testEmbeddedBinaryExists()
{
$notifier = $this->getNotifier();

$this->assertFileExists($notifier->getRootDir() . \DIRECTORY_SEPARATOR . $notifier->getEmbeddedBinary());
}

public function testExtraFilesExist()
{
$notifier = $this->getNotifier();

if (!$notifier->getExtraFiles()) {

$this->addToAssertionCount(1);

return;
}

foreach ($notifier->getExtraFiles() as $file) {
$this->assertFileExists($notifier->getRootDir() . \DIRECTORY_SEPARATOR . $file);
}
}
}
