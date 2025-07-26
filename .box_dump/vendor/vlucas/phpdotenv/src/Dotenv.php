<?php

declare(strict_types=1);

namespace Dotenv;

use Dotenv\Exception\InvalidPathException;
use Dotenv\Loader\Loader;
use Dotenv\Loader\LoaderInterface;
use Dotenv\Parser\Parser;
use Dotenv\Parser\ParserInterface;
use Dotenv\Repository\Adapter\ArrayAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use Dotenv\Store\StoreBuilder;
use Dotenv\Store\StoreInterface;
use Dotenv\Store\StringStore;

class Dotenv
{





private $store;






private $parser;






private $loader;






private $repository;











public function __construct(
StoreInterface $store,
ParserInterface $parser,
LoaderInterface $loader,
RepositoryInterface $repository
) {
$this->store = $store;
$this->parser = $parser;
$this->loader = $loader;
$this->repository = $repository;
}












public static function create(RepositoryInterface $repository, $paths, $names = null, bool $shortCircuit = true, ?string $fileEncoding = null)
{
$builder = $names === null ? StoreBuilder::createWithDefaultName() : StoreBuilder::createWithNoNames();

foreach ((array) $paths as $path) {
$builder = $builder->addPath($path);
}

foreach ((array) $names as $name) {
$builder = $builder->addName($name);
}

if ($shortCircuit) {
$builder = $builder->shortCircuit();
}

return new self($builder->fileEncoding($fileEncoding)->make(), new Parser(), new Loader(), $repository);
}











public static function createMutable($paths, $names = null, bool $shortCircuit = true, ?string $fileEncoding = null)
{
$repository = RepositoryBuilder::createWithDefaultAdapters()->make();

return self::create($repository, $paths, $names, $shortCircuit, $fileEncoding);
}











public static function createUnsafeMutable($paths, $names = null, bool $shortCircuit = true, ?string $fileEncoding = null)
{
$repository = RepositoryBuilder::createWithDefaultAdapters()
->addAdapter(PutenvAdapter::class)
->make();

return self::create($repository, $paths, $names, $shortCircuit, $fileEncoding);
}











public static function createImmutable($paths, $names = null, bool $shortCircuit = true, ?string $fileEncoding = null)
{
$repository = RepositoryBuilder::createWithDefaultAdapters()->immutable()->make();

return self::create($repository, $paths, $names, $shortCircuit, $fileEncoding);
}











public static function createUnsafeImmutable($paths, $names = null, bool $shortCircuit = true, ?string $fileEncoding = null)
{
$repository = RepositoryBuilder::createWithDefaultAdapters()
->addAdapter(PutenvAdapter::class)
->immutable()
->make();

return self::create($repository, $paths, $names, $shortCircuit, $fileEncoding);
}











public static function createArrayBacked($paths, $names = null, bool $shortCircuit = true, ?string $fileEncoding = null)
{
$repository = RepositoryBuilder::createWithNoAdapters()->addAdapter(ArrayAdapter::class)->make();

return self::create($repository, $paths, $names, $shortCircuit, $fileEncoding);
}













public static function parse(string $content)
{
$repository = RepositoryBuilder::createWithNoAdapters()->addAdapter(ArrayAdapter::class)->make();

$phpdotenv = new self(new StringStore($content), new Parser(), new Loader(), $repository);

return $phpdotenv->load();
}








public function load()
{
$entries = $this->parser->parse($this->store->read());

return $this->loader->load($this->repository, $entries);
}








public function safeLoad()
{
try {
return $this->load();
} catch (InvalidPathException $e) {

return [];
}
}








public function required($variables)
{
return (new Validator($this->repository, (array) $variables))->required();
}








public function ifPresent($variables)
{
return new Validator($this->repository, (array) $variables);
}
}
