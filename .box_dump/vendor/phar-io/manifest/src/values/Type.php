<?php declare(strict_types = 1);









namespace PharIo\Manifest;

use PharIo\Version\VersionConstraint;

abstract class Type {
public static function application(): Application {
return new Application;
}

public static function library(): Library {
return new Library;
}

public static function extension(ApplicationName $application, VersionConstraint $versionConstraint): Extension {
return new Extension($application, $versionConstraint);
}

/**
@psalm-assert-if-true */
public function isApplication(): bool {
return false;
}

/**
@psalm-assert-if-true */
public function isLibrary(): bool {
return false;
}

/**
@psalm-assert-if-true */
public function isExtension(): bool {
return false;
}
}
