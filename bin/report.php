<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaReport;
use Lemuria\Profiler;

require realpath(__DIR__ . '/../vendor/autoload.php');

putenv(Profiler::LEMURIA_ZERO_HOUR . '=' . microtime(true));

$fantasya = new FantasyaReport();
$fantasya->init();
if ($argc >= 2) {
	$fantasya->only($argv[1]);
}
$archives = $fantasya->createReports();
foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
$fantasya->logProfiler();
