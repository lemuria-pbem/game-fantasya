<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Map;

use function Lemuria\isBetween;
use Lemuria\Exception\LemuriaException;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Commodity\Luxury\Balsam;
use Lemuria\Model\Fantasya\Commodity\Luxury\Fur;
use Lemuria\Model\Fantasya\Commodity\Luxury\Gem;
use Lemuria\Model\Fantasya\Commodity\Luxury\Myrrh;
use Lemuria\Model\Fantasya\Commodity\Luxury\Oil;
use Lemuria\Model\Fantasya\Commodity\Luxury\Olibanum;
use Lemuria\Model\Fantasya\Commodity\Luxury\Silk;
use Lemuria\Model\Fantasya\Commodity\Luxury\Spice;
use Lemuria\Model\Fantasya\Commodity\Peasant;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Landscape\Desert;
use Lemuria\Model\Fantasya\Landscape\Forest;
use Lemuria\Model\Fantasya\Landscape\Glacier;
use Lemuria\Model\Fantasya\Landscape\Highland;
use Lemuria\Model\Fantasya\Landscape\Mountain;
use Lemuria\Model\Fantasya\Landscape\Plain;
use Lemuria\Model\Fantasya\Landscape\Swamp;
use Lemuria\Model\Fantasya\Luxuries;
use Lemuria\Model\Fantasya\Luxury;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Offer;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Tools\Lemuria\Map;

class LuxuryFinder
{
	use BuilderTrait;

	protected Region $region;

	protected array $data;

	protected array $luxuries;

	protected int $altitude;

	protected float $temperature;

	protected int $trees;

	public function __construct(protected Map $map) {
	}

	public function setRegion(Region $region): static {
		$this->region = $region;
		return $this;
	}

	public function setLuxuries(): void {
		$landscape = $this->region->Landscape();
		if ($landscape instanceof Navigable) {
			Lemuria::Log()->critical($landscape . ' ' . $this->region->Id() . ' will not have luxuries.');
			return;
		}
		$resources = $this->region->Resources();
		if ($landscape instanceof Glacier && !$resources[Peasant::class]->Count()) {
			Lemuria::Log()->critical('Glacier ' . $this->region->Id() . ' has no peasants and will not have luxuries.');
			return;
		}
		$this->altitude    = $this->map->Altitude();
		$this->temperature = $this->map->Temperature();
		$this->trees       = $resources[Wood::class]->Count();
		$this->luxuries    = array_fill(0, 7, null);

		match ($landscape::class) {
			Plain::class    => $this->calculateForPlain(),
			Forest::class   => $this->calculateForForest(),
			Highland::class => $this->calculateForHighland(),
			Mountain::class => $this->calculateForMountain(),
			Swamp::class    => $this->calculateForSwamp(),
			Desert::class   => $this->calculateForDesert(),
			Glacier::class  => $this->calculateForGlacier()
		};

		for ($i = 0; $i < 7; $i++) {
			$luxury = $this->luxuries[$i];
			if ($luxury) {
				$this->setLuxury($luxury);
				Lemuria::Log()->debug('Peasants in ' . $landscape . ' ' . $this->region->Id() . ' will produce ' . $luxury . '.');
				break;
			}
		}
		if ($i >= 7) {
			Lemuria::Log()->critical($landscape . ' ' . $this->region->Id() . ' has no conditions for any luxury.');
		}
	}

	protected function setLuxury(Luxury $luxury): void {
		$luxuries = new Luxuries(new Offer($luxury, $luxury->Value()));
		foreach (Luxuries::LUXURIES as $class) {
			$demand = $this->createLuxury($class);
			if ($demand !== $luxury) {
				$luxuries[$class] = new Offer($demand, $demand->Value());
			}
		}
		$this->region->setLuxuries($luxuries);
	}

	protected function calculateForPlain(): void {
		$this->checkForOil(0);
		$this->checkForSpice(1);
		$this->checkForSilk(2);
		$this->checkForMyrrh(3);
		$this->checkForFur(4);
		$this->checkForBalsam(4);
	}

	protected function calculateForForest(): void {
		$this->checkForBalsam(0);
		$this->checkForSilk(1);
		$this->checkForFur(4);
	}

	protected function calculateForHighland(): void {
		$this->checkForOlibanum(0);
		$this->checkForSpice(0);
		$this->checkForMyrrh(1);
		$this->checkForFur(2);
		$this->checkForGem(2);
		$this->checkForOil(2);
		$this->checkForBalsam(2);
		$this->checkForSilk(3);
	}

	protected function calculateForMountain(): void {
		$this->checkForGem(0);
		$this->checkForOlibanum(1);
		$this->checkForFur(2);
		$this->checkForMyrrh(2);
		$this->checkForOil(3);
		$this->checkForBalsam(3);
	}

	protected function calculateForSwamp(): void {
		$this->checkForSilk(0);
		$this->checkForBalsam(1);
		$this->checkForFur(6);
	}

	protected function calculateForDesert(): void {
		$this->checkForMyrrh(0);
		$this->checkForOil(1);
		$this->checkForOlibanum(2);
		$this->checkForSpice(2);
		$this->checkForFur(5);
	}

	protected function calculateForGlacier(): void {
		$this->checkForFur(0);
		$this->checkForGem(1);
	}

	protected function checkForBalsam(int $index): void {
		if ($this->temperature >= 20.0 && $this->trees >= 500) {
			$this->luxuries[$index] = self::createCommodity(Balsam::class);
		}
	}

	protected function checkForFur(int $index): void {
		$this->luxuries[$index] = self::createCommodity(Fur::class);
	}

	protected function checkForGem(int $index): void {
		if ($this->altitude >= 650) {
			$this->luxuries[$index] = self::createCommodity(Gem::class);
		}
	}

	protected function checkForMyrrh(int $index): void {
		if (isBetween(350, $this->altitude, 800) && $this->temperature >= 15.0 && $this->trees < 500) {
			$this->luxuries[$index] = self::createCommodity(Myrrh::class);
		}
	}

	protected function checkForOil(int $index): void {
		if ($this->temperature >= 15.0) {
			$this->luxuries[$index] = self::createCommodity(Oil::class);
		}
	}

	protected function checkForOlibanum(int $index): void {
		if ($this->trees < 300) {
			$this->luxuries[$index] = self::createCommodity(Olibanum::class);
		}
	}

	protected function checkForSilk(int $index): void {
		if ($this->altitude < 600 && isBetween(5.0, $this->temperature, 15.0) && $this->trees < 300) {
			$this->luxuries[$index] = self::createCommodity(Silk::class);
		}
	}

	protected function checkForSpice(int $index): void {
		if ($this->temperature >= 15.0 && $this->trees < 300) {
			$this->luxuries[$index] = self::createCommodity(Spice::class);
		}
	}

	private function createLuxury(string $class): Luxury {
		$luxury = self::createCommodity($class);
		if ($luxury instanceof Luxury) {
			return $luxury;
		}
		throw new LemuriaException('Invalid luxury: ' . $class);
	}
}
