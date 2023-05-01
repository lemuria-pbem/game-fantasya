<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaDebugger;

require realpath(__DIR__ . '/../vendor/autoload.php');

$archives = [];
$fantasya = new FantasyaDebugger();

$debugConfig = __DIR__ . DIRECTORY_SEPARATOR . 'debug-config.php';
if (file_exists($debugConfig)) {
	require $debugConfig;
	if (function_exists('configureDebugCherryPicker')) {
		configureDebugCherryPicker($fantasya->CherryPicker());
	}
}

try {
	$archives = $fantasya->init()->readOrders()->evaluate()->finish()->createReports();
} catch (\Throwable $e) {
	$fantasya->logException($e);
}
$fantasya->shutdown()->archiveLog();

foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
