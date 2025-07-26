<?php










namespace Joli\JoliNotif\tests\Driver;

use Joli\JoliNotif\Driver\BinaryProviderInterface;




trait BinaryProviderTestTrait
{
public function testRootDirectoryExists()
{

$driver = $this->getDriver();

$this->assertDirectoryExists($driver->getRootDir());
}

public function testEmbeddedBinaryExists()
{

$driver = $this->getDriver();

$this->assertFileExists($driver->getRootDir() . \DIRECTORY_SEPARATOR . $driver->getEmbeddedBinary());
}

public function testExtraFilesExist()
{

$driver = $this->getDriver();

if (!$driver->getExtraFiles()) {

$this->addToAssertionCount(1);

return;
}

foreach ($driver->getExtraFiles() as $file) {
$this->assertFileExists($driver->getRootDir() . \DIRECTORY_SEPARATOR . $file);
}
}
}
