<?php

declare(strict_types=1);

namespace Dotenv\Store;

final class StringStore implements StoreInterface
{





private $content;








public function __construct(string $content)
{
$this->content = $content;
}






public function read()
{
return $this->content;
}
}
