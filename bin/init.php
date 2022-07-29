<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\LemuriaAlpha;

require realpath(__DIR__ . '/../vendor/autoload.php');

$archives     = [];
$lemuriaAlpha = new LemuriaAlpha();
try {
	$archives = $lemuriaAlpha->init()->createReports()->createArchives();
} catch (\Throwable $e) {
	$lemuriaAlpha->logException($e);
}
$lemuriaAlpha->archiveLog();

foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
