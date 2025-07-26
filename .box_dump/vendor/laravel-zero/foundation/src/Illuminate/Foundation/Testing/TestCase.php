<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
use Concerns\InteractsWithContainer,
Concerns\MakesHttpRequests,
Concerns\InteractsWithAuthentication,
Concerns\InteractsWithConsole,
Concerns\InteractsWithDatabase,
Concerns\InteractsWithDeprecationHandling,
Concerns\InteractsWithExceptionHandling,
Concerns\InteractsWithSession,
Concerns\InteractsWithTime,
Concerns\InteractsWithTestCaseLifecycle,
Concerns\InteractsWithViews;






public function createApplication()
{
$app = require Application::inferBasePath().'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

return $app;
}






protected function setUp(): void
{
$this->setUpTheTestEnvironment();
}






protected function refreshApplication()
{
$this->app = $this->createApplication();
}








protected function tearDown(): void
{
$this->tearDownTheTestEnvironment();
}






public static function tearDownAfterClass(): void
{
static::tearDownAfterClassUsingTestCase();
}
}
