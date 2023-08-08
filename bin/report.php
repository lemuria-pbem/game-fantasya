<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaReport;
use Lemuria\Lemuria;
use Lemuria\Profiler;

require realpath(__DIR__ . '/../vendor/autoload.php');

putenv(Profiler::LEMURIA_ZERO_HOUR . '=' . microtime(true));

$fantasya = new FantasyaReport();
$archives = $fantasya->init()->createReports();
foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
Lemuria::Profiler()->logTotalPeak();
