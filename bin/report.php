<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaReport;

require realpath(__DIR__ . '/../vendor/autoload.php');

$fantasya = new FantasyaReport();
$archives = $fantasya->init()->createReports();
foreach ($archives as $archive) {
	echo $archive . PHP_EOL;
}
