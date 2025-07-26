<?php










use Joli\JoliNotif\DefaultNotifier;
use Joli\JoliNotif\Notification;

require __DIR__ . '/../vendor/autoload.php';

$notifier = new DefaultNotifier();

if (!$notifier->getDriver()) {
echo 'No supported notifier', \PHP_EOL;
exit(1);
}

$notification =
(new Notification())
->setTitle('Notification example')
->setBody('This is a notification example. Pretty cool isn\'t it?')
->setIcon(__DIR__ . '/icon-success.png')
;

$result = $notifier->send($notification);

$driver = $notifier->getDriver();

echo 'Notification ', $result ? 'successfully sent' : 'failed', ' with ', str_replace('Joli\JoliNotif\Driver\\', '', $driver::class), \PHP_EOL;
