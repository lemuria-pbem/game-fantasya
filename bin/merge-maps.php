<?php
declare(strict_types = 1);

use Lemuria\Alpha\AlphaConfig;
use Lemuria\Alpha\Map\Converter;
use Lemuria\Alpha\Model\World\ConvertedMap;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Storage\JsonProvider;
use Lemuria\Tools\Lemuria\Map;
use Lemuria\Tools\Lemuria\MapConfig;

require realpath(__DIR__ . '/../vendor/autoload.php');

$storage = realpath(__DIR__ . '/../storage');
if (!$storage) {
	throw new DirectoryNotFoundException($storage);
}

$config = new AlphaConfig($storage);
Lemuria::init($config->setLogFile('converter.log'));
Lemuria::Log()->debug('Loading Lemuria.', ['storage' => $storage]);

try {
	Lemuria::load();

	$convertedMap = new ConvertedMap();
	$convertedMap->unserialize(Lemuria::World()->serialize());
	$convertedMap->insertX(7)->addX(61)->insertY(68)->addY(19);
	Lemuria::Log()->debug('New map initialized.');

	$json      = new JsonProvider(__DIR__ . '/../doc/Map');
	$mapConfig = new MapConfig();
	$mapConfig->load($json->read('config.json'));
	$map       = new Map($mapConfig, $json->read('map.json'), Map::TYPE);
	$converter = new Converter($mapConfig, $map);
	Lemuria::Log()->debug('Map and MapConfig read.');

	for ($y = 0; $y < $mapConfig->width; $y++) {
		for ($x = 0; $x < $mapConfig->height; $x++) {
			if ($convertedMap->hasLocation($x, $y)) {
				Lemuria::Log()->critical('World already has a region at (' . $x . '/' . $y . ').');
			} else {
				$region = $converter->createRegion($x, $y);
				$convertedMap->setLocation($x, $y, $region);
				Lemuria::Log()->debug('New region ' . $region . ' added to world at (' . $x . '/' . $y . ').');
			}
		}
	}

	$json = new JsonProvider(__DIR__ . '/../storage/game');
	$json->write('world.json', $convertedMap->serialize());
	Lemuria::Log()->debug('New world saved.');

	$locations = [];
	foreach (Lemuria::Catalog()->getAll(Domain::LOCATION) as $location /* @var Region $location */) {
		$locations[] = $location->serialize();
	}
	$json->write('regions.json', $locations);
	Lemuria::Log()->debug('New locations saved.');
} catch (\Throwable $e) {
	Lemuria::Log()->emergency($e->getMessage(), ['exception' => $e]);
}
