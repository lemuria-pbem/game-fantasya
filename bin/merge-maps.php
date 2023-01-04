<?php
declare(strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaConfig;
use Lemuria\Game\Fantasya\FantasyaPathFactory;
use Lemuria\Game\Fantasya\Map\Converter;
use Lemuria\Game\Fantasya\Model\World\ConvertedMap;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Storage\JsonProvider;
use Lemuria\Renderer\Magellan\WorldInspector;
use Lemuria\Tools\Lemuria\Map;
use Lemuria\Tools\Lemuria\MapConfig;

require realpath(__DIR__ . '/../vendor/autoload.php');

$storage = realpath(__DIR__ . '/../storage');
if (!$storage) {
	throw new DirectoryNotFoundException($storage);
}

$config = new FantasyaConfig($storage);
Lemuria::init($config->setLogFile('converter.log'));
Lemuria::Log()->debug('Loading Lemuria.', ['storage' => $storage]);

try {
	Lemuria::load();

	$convertedMap = new ConvertedMap();
	$convertedMap->unserialize(Lemuria::World()->serialize());
	$convertedMap->insertX(13)->addX(51)->insertY(58)->addY(9 + 5);
	Lemuria::Log()->debug('New map initialized.');

	$json      = new JsonProvider(__DIR__ . '/../doc/Map');
	$mapConfig = new MapConfig();
	$mapConfig->load($json->read('config.json'));
	$map       = new Map($mapConfig, $json->read('map.json'), Map::TYPE);
	$converter = new Converter($mapConfig, $map);
	Lemuria::Log()->debug('Map and MapConfig read.');
	$converter->addChanges($json->read('changes.json'));
	Lemuria::Log()->debug('Map changes added.');

	for ($y = $mapConfig->offsetY; $y < $mapConfig->maxY + 5; $y++) {
		$v = $y - $mapConfig->offsetY;
		for ($x = $mapConfig->offsetX; $x < $mapConfig->maxX; $x++) {
			$h = $x - $mapConfig->offsetX;
			if ($convertedMap->hasLocation($h, $v)) {
				Lemuria::Log()->critical('World already has a region at (' . $h . '/' . $v . ').');
			} else {
				$region = $converter->createRegion($x, $y);
				$convertedMap->setLocation($h, $v, $region);
				Lemuria::Log()->debug('New region ' . $region . ' added to world at (' . $h . '/' . $v . ').');
			}
		}
	}

	$json = new JsonProvider($storage . '/game');
	$json->write('world.json', $convertedMap->serialize());
	Lemuria::Log()->debug('New world saved.');

	$locations = [];
	foreach (Lemuria::Catalog()->getAll(Domain::Location) as $location /* @var Region $location */) {
		$locations[] = $location->serialize();
	}
	$json->write('regions.json', $locations);
	Lemuria::Log()->debug('New locations saved.');

	$mapFile   = $storage . '/turn/world.cr';
	$inspector = new WorldInspector(new FantasyaPathFactory($storage));
	$inspector->setWorld($convertedMap)->setPath($mapFile)->render(new Id(0));
	Lemuria::Log()->debug('Magellan map saved to ' . realpath($mapFile) . '.');
} catch (\Throwable $e) {
	Lemuria::Log()->emergency($e->getMessage(), ['exception' => $e]);
}
