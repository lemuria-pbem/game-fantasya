<?php
declare(strict_types = 1);

use Lemuria\Game\Fantasya\MigrateConfig;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;

require realpath(__DIR__ . '/../vendor/autoload.php');

$storage = realpath(__DIR__ . '/../storage');
if (!$storage) {
	throw new DirectoryNotFoundException($storage);
}

$config = new MigrateConfig($storage);
Lemuria::init($config);
Lemuria::Game()->migrate();
