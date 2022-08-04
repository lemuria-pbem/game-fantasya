<?php
declare (strict_types = 1);

use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Game\Fantasya\FantasyaConfig;
use Lemuria\Lemuria;
use Lemuria\Id;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Storage\JsonProvider;
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
	if ($region->Landscape() !== $ocean) {
		$coordinates = Lemuria::World()->getCoordinates($region);
		$map->add($coordinates, $region);
	}
}

echo count($map) . ' islands on the map.' . PHP_EOL . PHP_EOL;
foreach ($map as $island /* @var Island $island */) {
	echo 'Island ' . $island->Id() . ': ' . $island->Origin() . ' - ' . $island->Width() . '/' . $island->Height() . '/' . $island->Size() . PHP_EOL;
	$ids   = [];
	$names = [];
	foreach ($island->getLocations() as $region) {
		$ids[]   = $region->Id()->Id();
		$names[] = $region->Name();
	}
	echo implode(',', $ids) . PHP_EOL;
	echo implode(', ', $names) . PHP_EOL;
	echo PHP_EOL;
}

$continents = [];
/** @var Continent $continent */
$continent = Lemuria::Catalog()->get(new Id(1), Domain::CONTINENT);
$continent->Landmass()->clear();
for ($id = 1; $id <= 88; $id++) {
	$continent->Landmass()->add(Region::get(new Id($id)));
}
$continents[] = $continent->serialize();

$continent = new Continent();
$continent->setName('Lemuria Beta');
$continent->setDescription('Ein riesiger, neu entstandener Kontinent.');
$continent->setId(Lemuria::Catalog()->nextId(Domain::CONTINENT));
for (; $id <= 5250; $id++) {
	$continent->Landmass()->add(Region::get(new Id($id)));
}
$continents[] = $continent->serialize();

$json = new JsonProvider(__DIR__ . '/../storage/game');
$json->write('continents.json', $continents);
