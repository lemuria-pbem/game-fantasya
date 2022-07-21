<?php
declare(strict_types = 1);
namespace Lemuria\Alpha\Map;

use Lemuria\Alpha\Map\Exception\MissingRegionException;
use Lemuria\Alpha\Map\Exception\RegionTypeException;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Landscape\Desert;
use Lemuria\Model\Fantasya\Landscape\Forest;
use Lemuria\Model\Fantasya\Landscape\Glacier;
use Lemuria\Model\Fantasya\Landscape\Highland;
use Lemuria\Model\Fantasya\Landscape\Mountain;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Landscape\Plain;
use Lemuria\Model\Fantasya\Landscape\Swamp;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Tools\Lemuria\Area;
use Lemuria\Tools\Lemuria\Map;
use Lemuria\Tools\Lemuria\Moisture;
use Lemuria\Tools\Lemuria\Terrain;

class Converter
{
	use BuilderTrait;

	protected Map $map;

	public function __construct(Map &$map) {
		$this->map = &$map;
	}

	public function createRegion(int $x, int $y): Region {
		$data = $this->map[$y][$x] ?? null;
		if (!$data) {
			throw new MissingRegionException($x, $y);
		}

		$type      = $data[Map::VEGETATION] ?? 0;
		$landscape = match ($type) {
			Terrain::OCEAN,    Moisture::LAKE,        Area::ICE           => Ocean::class,
			Terrain::PLAIN,    Area::TUNDRA                               => Plain::class,
			Area::RAIN_FOREST                                             => Forest::class,
			Terrain::HIGHLAND, Area::HIGH_DESERT,     Area::HIGH_FOREST   => Highland::class,
			Terrain::MOUNTAIN, Area::DESERT_MOUNTAIN, Area::RAIN_MOUNTAIN => Mountain::class,
			Moisture::MOOR                                                => Swamp::class,
			Moisture::OASIS,   Area::DESERT                               => Desert::class,
			Area::GLACIER                                                 => Glacier::class,
			default                                                       => throw new RegionTypeException($x, $y, $type)
		};

		$region = new Region();
		$region->setLandscape(self::createLandscape($landscape));
		return $region;
	}
}
