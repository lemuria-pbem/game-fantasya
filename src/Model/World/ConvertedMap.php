<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Model\World;

use Lemuria\Exception\LemuriaException;
use Lemuria\Model\Location;
use Lemuria\Model\Neighbours;
use Lemuria\Model\World\BaseMap;
use Lemuria\Model\World\Direction;
use Lemuria\Model\World\MapCoordinates;
use Lemuria\Model\World\Path;

class ConvertedMap extends BaseMap
{
	public function getDistance(Location $from, Location $to): int {
		throw new LemuriaException('Not implemented.');
	}

	public function getNeighbours(Location $location): Neighbours {
		throw new LemuriaException('Not implemented.');
	}

	public function getPath(Location $start, Direction $direction, int $distance): Path {
		throw new LemuriaException('Not implemented.');
	}

	public function insertX(int $count): ConvertedMap {
		$h = count($this->map);
		for ($y = 0; $y < $h; $y++) {
			$row           = array_fill(0, $count, null);
			$this->map[$y] = array_merge($row, $this->map[$y]);
		}
		foreach ($this->coordinates as $id => $coordinate) {
			$this->coordinates[$id] = new MapCoordinates($coordinate->X() + $count, $coordinate->Y());
		}
		return $this;
	}

	public function addX(int $count): ConvertedMap {
		$h   = count($this->map);
		$row = array_fill(0, $count, null);
		for ($y = 0; $y < $h; $y++) {
			$this->map[$y] = array_merge($this->map[$y], $row);
		}
		return $this;
	}

	public function insertY(int $count): ConvertedMap {
		$w         = count($this->map[0]);
		$row       = array_fill(0, $w, null);
		$rows      = array_fill(0, $count, $row);
		$this->map = array_merge($rows, $this->map);
		foreach ($this->coordinates as $id => $coordinate) {
			$this->coordinates[$id] = new MapCoordinates($coordinate->X(), $coordinate->Y() + $count);
		}
		return $this;
	}

	public function addY(int $count): ConvertedMap {
		$w         = count($this->map[0]);
		$row       = array_fill(0, $w, null);
		$rows      = array_fill(0, $count, $row);
		$this->map = array_merge($this->map, $rows);
		return $this;
	}

	public function hasLocation(int $x, int $y): bool {
		return isset($this->map[$y][$x]);
	}

	public function setLocation(int $x, int $y, Location $location): ConvertedMap {
		$id                     = $location->Id()->Id();
		$this->map[$y][$x]      = $id;
		$this->coordinates[$id] = new MapCoordinates($x, $y);
		return $this;
	}

	protected function getNeighbourCoordinates(Location $location): array {
		throw new LemuriaException('Not implemented.');
	}
}
