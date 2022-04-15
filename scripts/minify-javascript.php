<?php
declare(strict_types = 1);

use MatthiasMullie\Minify\JS;

require realpath(__DIR__ . '/../vendor/autoload.php');

$minifier = new JS();
for ($i = 1; $i < $argc; $i++) {
	$minifier->add($argv[$i]);
}
echo $minifier->minify();
