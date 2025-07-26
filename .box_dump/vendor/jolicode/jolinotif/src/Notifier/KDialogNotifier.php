<?php










namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Driver\KDialogDriver;
use Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', KDialogNotifier::class);







class KDialogNotifier extends KDialogDriver implements Notifier
{
}
