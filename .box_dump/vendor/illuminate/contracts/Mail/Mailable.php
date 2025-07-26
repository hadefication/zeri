<?php

namespace Illuminate\Contracts\Mail;

use Illuminate\Contracts\Queue\Factory as Queue;

interface Mailable
{






public function send($mailer);







public function queue(Queue $queue);








public function later($delay, Queue $queue);








public function cc($address, $name = null);








public function bcc($address, $name = null);








public function to($address, $name = null);







public function locale($locale);







public function mailer($mailer);
}
