<?php
declare (strict_types = 1);

use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Game\Fantasya\FantasyaConfig;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\World\Island\Island;
use Lemuria\Model\World\Island\Map;

require realpath(__DIR__ . '/../vendor/autoload.php');

$storage = realpath(__DIR__ . '/../storage');
if (!$storage) {
	throw new DirectoryNotFoundException($storage);
}

$config = new FantasyaConfig($storage);
Lemuria::init($config->setLogFile('debug.log'));
Lemuria::load();

$map   = new Map(Lemuria::World());
$ocean = Lemuria::Builder()->create(Ocean::class);
foreach (Lemuria::Catalog()->getAll(Domain::LOCATION) as $region /* @var Region $region */) {
	if ($region->Landscape() === $ocean) {
		$coordinates = Lemuria::World()->getCoordinates($region);
		$map->add($coordinates, $region);
	}
}

echo count($map) . ' water areas on the map.' . PHP_EOL . PHP_EOL;
foreach ($map as $island /* @var Island $island */) {
	echo 'Area ' . $island->Id() . ': ' . $island->Origin() . ' - ' . $island->Width() . '/' . $island->Height() . '/' . $island->Size() . PHP_EOL;
	foreach ($island->getLocations() as $region) {
		echo $region->Name() . ', ';
	}
	echo PHP_EOL;
}
