<?php









namespace Mockery\Adapter\Phpunit;

use LogicException;
use Mockery;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\BaseTestRunner;
use PHPUnit\Util\Blacklist;
use ReflectionClass;

use function dirname;
use function method_exists;
use function sprintf;

class TestListenerTrait
{







public function endTest(Test $test, $time)
{
if (! $test instanceof TestCase) {


return;
}

if ($test->getStatus() !== BaseTestRunner::STATUS_PASSED) {




return;
}

try {


Mockery::self();
} catch (LogicException $logicException) {
return;
}

$e = new ExpectationFailedException(
sprintf(
"Mockery's expectations have not been verified. Make sure that \Mockery::close() is called at the end of the test. Consider using %s\MockeryPHPUnitIntegration or extending %s\MockeryTestCase.",
__NAMESPACE__,
__NAMESPACE__
)
);


$result = $test->getTestResultObject();

if ($result !== null) {
$result->addFailure($test, $e, $time);
}
}

public function startTestSuite()
{
if (method_exists(Blacklist::class, 'addDirectory')) {
(new Blacklist())->getBlacklistedDirectories();
Blacklist::addDirectory(dirname((new ReflectionClass(Mockery::class))->getFileName()));
} else {
Blacklist::$blacklistedClassNames[Mockery::class] = 1;
}
}
}
