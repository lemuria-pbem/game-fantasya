<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Map;

use function Lemuria\getNamespace;
use Lemuria\Engine\Fantasya\Factory\Workplaces;
use Lemuria\Exception\LemuriaException;
use Lemuria\Factory\Namer;
use Lemuria\Game\Fantasya\Map\Exception\MissingRegionException;
use Lemuria\Game\Fantasya\Map\Exception\RegionTypeException;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Commodity\Camel;
use Lemuria\Model\Fantasya\Commodity\Elephant;
use Lemuria\Model\Fantasya\Commodity\Griffin;
use Lemuria\Model\Fantasya\Commodity\Horse;
use Lemuria\Model\Fantasya\Commodity\Iron;
use Lemuria\Model\Fantasya\Commodity\Peasant;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Commodity\Stone;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Landscape\Desert;
use Lemuria\Model\Fantasya\Landscape\Forest;
use Lemuria\Model\Fantasya\Landscape\Glacier;
use Lemuria\Model\Fantasya\Landscape\Highland;
use Lemuria\Model\Fantasya\Landscape\Lake;
use Lemuria\Model\Fantasya\Landscape\Mountain;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Landscape\Plain;
use Lemuria\Model\Fantasya\Landscape\Swamp;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Singleton;
use Lemuria\Tools\Lemuria\Area;
use Lemuria\Tools\Lemuria\Good;
use Lemuria\Tools\Lemuria\Map;
use Lemuria\Tools\Lemuria\MapConfig;
use Lemuria\Tools\Lemuria\Moisture;
use Lemuria\Tools\Lemuria\Terrain;

class Converter
{
	use BuilderTrait;

	protected const WORKPLACES = 10000;

	protected const MAX_SILVER = 6 * 3 * 10;

	protected const MAX_STONE = 130;

	protected const MAX_IRON = 110;

	protected const MAX_GRIFFINS = 30;

	protected const MIN_RESOURCES = 0.33;

	protected const STONE_FACTOR = [Desert::class => 0.13, Highland::class => 0.9, Mountain::class => 1.0, Glacier::class => 0.45];

	protected const IRON_FACTOR = [Highland::class => 1.0, Mountain::class => 0.9, Glacier::class => 0.35];

	protected readonly Dictionary $dictionary;

	protected readonly HerbFinder $herbFinder;

	protected readonly LuxuryFinder $luxuryFinder;

	protected readonly float $maximum;

	protected array $changes = [];

	private Namer $namer;

	public static function convertLandscape(int $vegetation): ?string {
		return match ($vegetation) {
			Terrain::OCEAN,    Area::ICE                                  => Ocean::class,
			Terrain::PLAIN,    Area::TUNDRA                               => Plain::class,
			Area::RAIN_FOREST                                             => Forest::class,
			Terrain::HIGHLAND, Area::HIGH_DESERT,     Area::HIGH_FOREST   => Highland::class,
			Terrain::MOUNTAIN, Area::DESERT_MOUNTAIN, Area::RAIN_MOUNTAIN => Mountain::class,
			Moisture::MOOR                                                => Swamp::class,
			Moisture::LAKE                                                => Lake::class,
			Moisture::OASIS,   Area::DESERT                               => Desert::class,
			Area::GLACIER                                                 => Glacier::class,
			default                                                       => null
		};
	}

	public function __construct(protected MapConfig $config, protected Map $map) {
		$this->dictionary   = new Dictionary();
		$this->luxuryFinder = new LuxuryFinder($map);
		$this->herbFinder   = new HerbFinder($map);
		$this->namer        = Lemuria::Namer();
		$this->maximum      = $config->square * 100.0;
	}

	public function addChanges(array $changes): static {
		foreach ($changes as $change) {
			if (!isset($change['x']) || !isset($change['y'])) {
				throw new LemuriaException('Missing coordinates in changes.');
			}
			$this->changes[$change['y']][$change['x']] = $change;
		}
		return $this;
	}

	public function createRegion(int $x, int $y): Region {
		$data = $this->map[$y][$x] ?? null;
		if (!$data) {
			throw new MissingRegionException($x, $y);
		}
		$this->map->setX($x)->setY($y);

		$id            = Lemuria::Catalog()->nextId(Domain::Location);
		$landscape     = $this->getLandscape($x, $y, $data[Map::VEGETATION] ?? 0);
		$landscapeItem = self::createLandscape($landscape);

		$region = new Region();
		$region->setId($id);
		$region->setLandscape($landscapeItem);
		$region->setName($this->namer->name($region));
		if (!($landscapeItem instanceof Navigable)) {
			$peasants = $this->calculatePeasants($landscape, $data[Map::GOOD]);
			$animal   = $this->getAnimal($landscape);
			$this->addResources([
				Peasant::class => $peasants,
				Silver::class  => $this->calculateSilver($peasants, $data[Map::FERTILITY]),
				Wood::class    => $this->calculateForest($landscape, $data[Map::GOOD]),
				Stone::class   => $this->calculateStone($landscape, $data),
				Iron::class    => $this->calculateIron($landscape, $data),
				$animal        => $this->calculateAnimals($animal, $data[Map::GOOD], $data[Map::ALTITUDE])
			], $region);
			$this->setLuxuries($region);
			$this->setHerbage($region, $x, $y);
		}
		return $region;
	}

	protected function getLandscape(int $x, int $y, int $vegetation): string {
		$h = $x - $this->config->offsetX;
		$v = $y - $this->config->offsetY;
		if (isset($this->changes[$v][$h]['landscape'])) {
			return $this->parseLandscape($this->changes[$v][$h]['landscape']);
		}
		$landscape = self::convertLandscape($vegetation);
		if ($landscape) {
			return $landscape;
		}
		throw new RegionTypeException($x, $y, $vegetation);
	}

	protected function getAnimal(string $landscape): ?string {
		return match ($landscape) {
			Plain::class                   => Horse::class,
			Highland::class, Desert::class => Camel::class,
			Forest::class, Swamp::class    => Elephant::class,
			Glacier::class                 => Griffin::class,
			default                        => Singleton::class
		};
	}

	protected function calculatePeasants(string $landscape, array $goods): int {
		$factor = match ($landscape) {
			Glacier::class => 0.0,
			Plain::class   => 1.5,
			Swamp::class   => 0.25,
			default        => 1.0
		};

		$fishing  = $goods[Good::FISH] / ($this->maximum / $this->config->hunting);
		$farming  = $goods[Good::CROP] / ($this->maximum / $this->config->farming);
		$breeding = $goods[Good::MEAT] / ($this->maximum / $this->config->breeding);
		return (int)floor($factor * ($fishing + $farming + $breeding) * self::WORKPLACES);
	}

	protected function calculateSilver(int $peasants, float $fertility): int {
		return (int)floor($peasants * self::MAX_SILVER * $fertility);
	}

	protected function calculateForest(string $landscape, array $goods): int {
		$forest = $goods[Good::WOOD] / ($this->maximum / $this->config->forestry);
		$trees  = (int)floor($forest * self::WORKPLACES / Workplaces::TREE);
		if ($landscape === Forest::class) {
			if ($trees < Forest::TREES) {
				$trees = rand(600, $trees < 50 ? 649 : 699);
			}
		}
		return $trees;
	}

	protected function calculateStone(string $landscape, array $data): int {
		$factor = self::STONE_FACTOR[$landscape] ?? null;
		if (!$factor) {
			return 0;
		}

		$difference = max(0, $data[Map::ALTITUDE] - $this->config->highland);
		$maximum    = $this->config->maxHeight + $this->config->maxDiff - $this->config->highland;
		$percentage = 1.0 - min($difference / $maximum, 1.0);
		$minimum    = self::MIN_RESOURCES * self::MAX_STONE;
		$stone      = $minimum + $percentage * (self::MAX_STONE - $minimum);
		$stone     *= $factor;
		return (int)floor($stone);
	}

	protected function calculateIron(string $landscape, array $data): int {
		$factor = self::IRON_FACTOR[$landscape] ?? null;
		if (!$factor) {
			return 0;
		}

		$difference = max(0, $data[Map::ALTITUDE] - $this->config->highland);
		$maximum    = $this->config->maxHeight + $this->config->maxDiff - $this->config->highland;
		$percentage = min($difference / $maximum, 1.0);
		$minimum    = self::MIN_RESOURCES * self::MAX_IRON;
		$iron       = $minimum + $percentage * (self::MAX_IRON - $minimum);
		$iron      *= $factor;
		return (int)floor($iron);
	}

	protected function calculateAnimals(string $animal, array $goods, int $altitude): int {
		if ($animal === Griffin::class) {
			$difference = max(0, $altitude - $this->config->mountain);
			$maximum    = $this->config->maxHeight + $this->config->maxDiff;
			$percentage = min($difference / $maximum, 1.0);
			return (int)floor($percentage * self::MAX_GRIFFINS);
		}

		$animals    = $goods[Good::GAME] / 10.0 / ($this->maximum / $this->config->hunting);
		$workplaces = match ($animal) {
			Horse::class    => Workplaces::HORSE * 1.0,
			Camel::class    => Workplaces::CAMEL * 3.0,
			Elephant::class => Workplaces::ELEPHANT * 2.0,
			default         => null
		};
		return $workplaces ? (int)floor($animals * self::WORKPLACES / $workplaces) : 0;
	}

	protected function addResources(array $resources, Region $region): void {
		foreach ($resources as $class => $count) {
			if ($count > 0) {
				$region->Resources()->add(new Quantity(self::createCommodity($class), $count));
			}
		}
	}

	protected function setLuxuries(Region $region): void {
		$this->luxuryFinder->setRegion($region)->setLuxuries();
	}

	protected function setHerbage(Region $region, int $x, int $y): void {
		$herb = $this->changes[$y][$x]['herb'] ?? null;
		$this->herbFinder->setRegion($region)->findNeighbours($x, $y)->setHerbage($herb);
	}

	protected function parseLandscape(string $landscape): string {
		return getNamespace(Ocean::class) . '\\' . $landscape;
	}
}
