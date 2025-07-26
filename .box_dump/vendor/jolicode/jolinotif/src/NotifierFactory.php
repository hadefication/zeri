<?php










namespace Joli\JoliNotif;

use Joli\JoliNotif\Exception\NoSupportedNotifierException;
use Joli\JoliNotif\Notifier\AppleScriptNotifier;
use Joli\JoliNotif\Notifier\GrowlNotifyNotifier;
use Joli\JoliNotif\Notifier\KDialogNotifier;
use Joli\JoliNotif\Notifier\LibNotifyNotifier;
use Joli\JoliNotif\Notifier\NotifuNotifier;
use Joli\JoliNotif\Notifier\NotifySendNotifier;
use Joli\JoliNotif\Notifier\SnoreToastNotifier;
use Joli\JoliNotif\Notifier\TerminalNotifierNotifier;
use Joli\JoliNotif\Notifier\WslNotifySendNotifier;
use JoliCode\PhpOsHelper\OsHelper;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0. Use the %s class directly', NotifierFactory::class, DefaultNotifier::class);




class NotifierFactory
{



public static function create(array $notifiers = []): Notifier
{
return new LegacyNotifier($notifiers);
}




public static function createOrThrowException(array $notifiers = []): Notifier
{
$legacyNotifier = new LegacyNotifier($notifiers);

if (!$legacyNotifier->getDriver()) {
throw new NoSupportedNotifierException();
}

return $legacyNotifier;
}




public static function getDefaultNotifiers(): array
{


if (OsHelper::isUnix() && !OsHelper::isWindowsSubsystemForLinux()) {
return self::getUnixNotifiers();
}

return self::getWindowsNotifiers();
}




private static function getUnixNotifiers(): array
{
return [
new LibNotifyNotifier(),
new GrowlNotifyNotifier(),
new TerminalNotifierNotifier(),
new AppleScriptNotifier(),
new KDialogNotifier(),
new NotifySendNotifier(),
];
}




private static function getWindowsNotifiers(): array
{
return [
new SnoreToastNotifier(),
new NotifuNotifier(),
new WslNotifySendNotifier(),
];
}
}
