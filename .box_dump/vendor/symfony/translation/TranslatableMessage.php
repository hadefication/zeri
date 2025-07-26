<?php










namespace Symfony\Component\Translation;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;




class TranslatableMessage implements TranslatableInterface
{
public function __construct(
private string $message,
private array $parameters = [],
private ?string $domain = null,
) {
}

public function __toString(): string
{
return $this->getMessage();
}

public function getMessage(): string
{
return $this->message;
}

public function getParameters(): array
{
return $this->parameters;
}

public function getDomain(): ?string
{
return $this->domain;
}

public function trans(TranslatorInterface $translator, ?string $locale = null): string
{
return $translator->trans($this->getMessage(), array_map(
static fn ($parameter) => $parameter instanceof TranslatableInterface ? $parameter->trans($translator, $locale) : $parameter,
$this->getParameters()
), $this->getDomain(), $locale);
}
}
