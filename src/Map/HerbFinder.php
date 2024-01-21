<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Map;

use Lemuria\Exception\LemuriaException;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Factory\HerbGenerator;
use Lemuria\Model\Fantasya\Herb;
use Lemuria\Model\Fantasya\Herbage;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Tools\Lemuria\Map;

class HerbFinder
{
	use BuilderTrait;

	protected HerbGenerator $generator;

	protected Region $region;

	public function __construct(protected Map $map) {
	}

	public function setRegion(Region $region): static {
		$this->region = $region;
		$this->generator->setRegion($region);
		return $this;
	}

	public function findNeighbours(int $x, int $y): static {
		$this->generator->setNeighbours(
			$this->getNeighbourLandscape($this->map[++$y][--$x][Map::VEGETATION] ?? 0),
			$this->getNeighbourLandscape($this->map[$y][++$x][Map::VEGETATION] ?? 0),
			$this->getNeighbourLandscape($this->map[--$y][++$x][Map::VEGETATION] ?? 0),
			$this->getNeighbourLandscape($this->map[--$y][$x][Map::VEGETATION] ?? 0),
			$this->getNeighbourLandscape($this->map[$y][--$x][Map::VEGETATION] ?? 0),
			$this->getNeighbourLandscape($this->map[++$y][--$x][Map::VEGETATION] ?? 0)
		);
		return $this;
	}

	public function setHerbage(string $herb = null): void {
		$landscape = $this->region->Landscape();
		if ($herb) {
			$herb = self::createHerb($herb);
		} else {
			$herb = $this->generator->setRegion($this->region)->getHerb();
		}
		$herbage = new Herbage($herb);
		$herbage->setOccurrence(0.5 + (rand(0, 1000000) - 500000) / 10000000);
		$this->region->setHerbage($herbage);
		Lemuria::Log()->debug($landscape . ' ' . $this->region->Id() . ' has ' . $herbage->Herb() . ' (' . $herbage->Occurrence() . ').');
	}

	protected function getNeighbourLandscape(int $vegetation): string {
		$landscape = Converter::convertLandscape($vegetation);
		if ($landscape) {
			return $landscape;
		}
		throw new LemuriaException('Could not convert vegetation ' . $vegetation . ' to landscape.');
	}

	private function createHerb(string $class): Herb {
		$herb = self::createCommodity($class);
		if ($herb instanceof Herb) {
			return $herb;
		}
		throw new LemuriaException('Invalid herb ' . $herb);
	}
}
