<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaGame;

require realpath(__DIR__ . '/../vendor/autoload.php');

$archives = [];
$fantasya = new FantasyaGame();
try {
	$archives = $fantasya->init()->createReports();
} catch (\Throwable $e) {
	$fantasya->logException($e);
}
$fantasya->archiveLog();

foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
