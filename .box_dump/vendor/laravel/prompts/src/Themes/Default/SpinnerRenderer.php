<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Spinner;

class SpinnerRenderer extends Renderer
{





protected array $frames = ['⠂', '⠒', '⠐', '⠰', '⠠', '⠤', '⠄', '⠆'];




protected string $staticFrame = '⠶';




protected int $interval = 75;




public function __invoke(Spinner $spinner): string
{
if ($spinner->static) {
return $this->line(" {$this->cyan($this->staticFrame)} {$spinner->message}");
}

$spinner->interval = $this->interval;

$frame = $this->frames[$spinner->count % count($this->frames)];

return $this->line(" {$this->cyan($frame)} {$spinner->message}");
}
}
