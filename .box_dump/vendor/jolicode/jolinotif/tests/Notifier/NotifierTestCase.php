<?php










namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notifier;
use PHPUnit\Framework\TestCase;

/**
@group
*/
abstract class NotifierTestCase extends TestCase
{
abstract protected function getNotifier(): Notifier;

protected function getIconDir(): string
{
return realpath(\dirname(__DIR__) . '/fixtures');
}










protected function invokeMethod($object, string $methodName, array $parameters = [])
{
$reflection = new \ReflectionClass($object::class);
$method = $reflection->getMethod($methodName);
$method->setAccessible(true);

return $method->invokeArgs($object, $parameters);
}
}
