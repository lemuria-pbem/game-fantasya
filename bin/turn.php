<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaGame;
use Lemuria\Profiler;

require realpath(__DIR__ . '/../vendor/autoload.php');

if (!getenv(Profiler::LEMURIA_ZERO_HOUR)) {
	putenv(Profiler::LEMURIA_ZERO_HOUR . '=' . microtime(true));
}

$archives = [];
$fantasya = new FantasyaGame();
try {
	$archives = $fantasya->init()->readOrders()->initiate()->evaluate()->finish()->createReports();
} catch (\Throwable $e) {
	$fantasya->logException($e);
}
$fantasya->shutdown()->archiveLog();

foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
