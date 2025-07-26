<?php









namespace Mockery\Generator;

class CachingGenerator implements Generator
{



protected $cache = [];




protected $generator;

public function __construct(Generator $generator)
{
$this->generator = $generator;
}




public function generate(MockConfiguration $config)
{
$hash = $config->getHash();

if (array_key_exists($hash, $this->cache)) {
return $this->cache[$hash];
}

return $this->cache[$hash] = $this->generator->generate($config);
}
}
