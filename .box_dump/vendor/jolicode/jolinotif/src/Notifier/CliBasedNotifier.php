<?php










namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Driver\AbstractCliBasedDriver;
use Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', CliBasedNotifier::class);




abstract class CliBasedNotifier extends AbstractCliBasedDriver implements Notifier
{
}
