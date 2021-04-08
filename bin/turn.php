<?php
declare (strict_types = 1);

use Lemuria\Alpha\LemuriaAlpha;

require realpath(__DIR__ . '/../vendor/autoload.php');

$archives     = [];
$lemuriaAlpha = new LemuriaAlpha();
try {
	$archives = $lemuriaAlpha->init()->readOrders()->initiate()->evaluate()->finish()->createReports()->createArchives();
} catch (\Throwable $e) {
	$lemuriaAlpha->logException($e);
}
$lemuriaAlpha->archiveLog();

foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
