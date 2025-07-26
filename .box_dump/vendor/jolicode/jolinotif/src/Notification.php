<?php










namespace Joli\JoliNotif;

use Joli\JoliNotif\Util\PharExtractor;
use JoliCode\PhpOsHelper\OsHelper;

class Notification
{
private ?string $title = null;
private ?string $body = null;
private ?string $icon = null;

private array $options = [];

public function getTitle(): ?string
{
return $this->title;
}

public function setTitle(string $title): self
{
$this->title = $title;

return $this;
}

public function getBody(): ?string
{
return $this->body;
}

public function setBody(string $body): self
{
$this->body = $body;

return $this;
}

public function getIcon(): ?string
{
return $this->icon;
}

public function setIcon(string $icon): self
{

if (PharExtractor::isLocatedInsideAPhar($icon)) {
$icon = PharExtractor::extractFile($icon);
} else {

$icon = OsHelper::isWindowsSubsystemForLinux()
? preg_replace('/^\/mnt\/([a-z])\//', '$1:\\', $icon, 1)
: realpath($icon);
}

$this->icon = \is_string($icon) ? $icon : null;

return $this;
}

public function getOption(string $key): string|int|null
{
return $this->options[$key] ?? null;
}

public function addOption(string $key, string|int $option): self
{
$this->options[$key] = $option;

return $this;
}
}
