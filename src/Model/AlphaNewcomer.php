<?php
declare(strict_types = 1);
namespace Lemuria\Alpha\Model;

use function Lemuria\getClass;
use Lemuria\Id;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Landscape;
use Lemuria\Model\Fantasya\Race;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Resources;
use Lemuria\Model\Newcomer;
use Lemuria\Serializable;
use Lemuria\SerializableTrait;

class AlphaNewcomer implements Newcomer, Serializable
{
	use BuilderTrait;
	use SerializableTrait;

	private string $uuid;

	private int $creation;

	private string $name;

	private string $description;

	private ?Race $race = null;

	private ?Landscape $landscape = null;

	private ?Id $origin = null;

	private Resources $inventory;

	public function __construct() {
		$this->creation  = time();
		$this->inventory = new Resources();
	}

	public function Uuid(): string {
		return $this->uuid;
	}

	public function Creation(): int {
		return $this->creation;
	}

	public function Name(): string {
		return $this->name;
	}

	public function Description(): string {
		return $this->description;
	}

	public function Race(): ?Race {
		return $this->race;
	}

	public function Landscape(): ?Landscape {
		return $this->landscape;
	}

	public function Origin(): ?Region {
		return $this->origin ? Region::get($this->origin) : null;
	}

	public function Inventory(): Resources {
		return $this->inventory;
	}

	public function serialize(): array {
		$data = ['uuid'        => $this->uuid, 'creation' => $this->creation,
				 'name'        => $this->name, 'description' => $this->description,
				 'race'        => $this->race ? getClass($this->race) : null,
				 'landscape'   => $this->landscape ? getClass($this->landscape) : null,
				 'origin'      => $this->origin?->Id(),
				 'inventory'   => $this->inventory->serialize()
		];
		return $data;
	}

	public function unserialize(array $data): Serializable {
		$this->uuid        = $data['uuid'];
		$this->creation    = $data['creation'];
		$this->name        = $data['name'];
		$this->description = $data['description'];
		$this->race        = $data['race'] ? self::createRace($data['race']) : null;
		$this->landscape   = $data['landscape'] ? self::createLandscape($data['landscape']) : null;
		$this->origin      = $data['origin'] ? new Id($data['origin']) : null;
		$this->inventory->unserialize($data['inventory']);
		return $this;
	}

	public function setName(string $name): AlphaNewcomer {
		$this->name = $name;
		return $this;
	}

	public function setDescription(string $description): AlphaNewcomer {
		$this->description = $description;
		return $this;
	}

	public function setRace(?Race $race): AlphaNewcomer {
		$this->race = $race;
		return $this;
	}

	public function setLandscape(?Landscape $landscape): AlphaNewcomer {
		$this->landscape = $landscape;
		return $this;
	}

	public function setOrigin(?Region $region): AlphaNewcomer {
		$this->origin = $region?->Id();
		return $this;
	}

	protected function validateSerializedData(array &$data): void {
		$this->validate($data, 'uuid', 'string');
		$this->validate($data, 'creation', 'int');
		$this->validate($data, 'name', 'string');
		$this->validate($data, 'description', 'string');
		$this->validate($data, 'race', '?string');
		$this->validate($data, 'landscape', '?string');
		$this->validate($data, 'origin', '?int');
		$this->validate($data, 'inventory', 'array');
	}
}
