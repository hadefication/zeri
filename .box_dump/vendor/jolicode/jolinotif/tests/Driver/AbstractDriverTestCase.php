<?php










namespace Joli\JoliNotif\tests\Driver;

use Joli\JoliNotif\Driver\DriverInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractDriverTestCase extends TestCase
{
public function getIconDir(): string
{
return realpath(\dirname(__DIR__) . '/fixtures');
}

abstract protected function getDriver(): DriverInterface;










protected function invokeMethod($object, string $methodName, array $parameters = [])
{
$reflection = new \ReflectionClass($object::class);
$method = $reflection->getMethod($methodName);

return $method->invokeArgs($object, $parameters);
}
}
