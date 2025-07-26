<?php

































declare(strict_types=1);










namespace Pest\Logging\TeamCity\Subscriber;

use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\Test\SkippedSubscriber;
use ReflectionClass;




final class TestSkippedSubscriber extends Subscriber implements SkippedSubscriber
{
public function notify(Skipped $event): void
{
if (str_contains($event->message(), '__TODO__')) {
$this->printTodoItem();
}

$this->logger()->testSkipped($event);
}




private function printTodoItem(): void
{
$mirror = new ReflectionClass($this->printer());
$printerMirror = $mirror->getMethod('printProgress');
$printerMirror->invoke($this->printer(), 'T');
}
}
