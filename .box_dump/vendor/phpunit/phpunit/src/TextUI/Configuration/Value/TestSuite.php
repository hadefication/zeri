<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@no-named-arguments
@immutable

*/
final readonly class TestSuite
{



private string $name;
private TestDirectoryCollection $directories;
private TestFileCollection $files;
private FileCollection $exclude;




public function __construct(string $name, TestDirectoryCollection $directories, TestFileCollection $files, FileCollection $exclude)
{
$this->name = $name;
$this->directories = $directories;
$this->files = $files;
$this->exclude = $exclude;
}




public function name(): string
{
return $this->name;
}

public function directories(): TestDirectoryCollection
{
return $this->directories;
}

public function files(): TestFileCollection
{
return $this->files;
}

public function exclude(): FileCollection
{
return $this->exclude;
}
}
