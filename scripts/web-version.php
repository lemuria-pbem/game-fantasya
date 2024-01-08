<?php
declare(strict_types = 1);

use Lemuria\Version\VersionFinder;

require __DIR__ . '/../vendor/autoload.php';

$versionFinder = new VersionFinder(__DIR__ . '/..');
$version       = explode('.', $versionFinder->get()->version);
echo $version[0] . $version[1];
