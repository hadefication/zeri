<?php










namespace Joli\JoliNotif;

use Joli\JoliNotif\Driver\DriverInterface;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" interface is deprecated and will be removed in 3.0. Use "%s" instead.', Notifier::class, NotifierInterface::class);




interface Notifier extends NotifierInterface, DriverInterface
{
}
