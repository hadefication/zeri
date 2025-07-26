<?php










namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Driver\SnoreToastDriver;
use Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', SnoreToastNotifier::class);







class SnoreToastNotifier extends SnoreToastDriver implements Notifier
{
}
