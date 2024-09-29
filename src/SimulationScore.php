<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Effect;
use Lemuria\Engine\Fantasya\Effect\AbstractPartyEffect;
use Lemuria\Engine\Fantasya\Effect\AbstractUnitEffect;
use Lemuria\Engine\Score;
use Lemuria\Identifiable;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;

final class SimulationScore implements Score
{
	/**
	 * @var array<Effect>
	 */
	private array $effects;

	private int $index = 0;

	private int $count = 0;

	public function __construct(private readonly ?Party $party = null) {
	}

	public function current(): Effect {
		return $this->effects[$this->index];
	}

	public function key(): string {
		return (string)$this->effects[$this->index];
	}

	public function next(): void {
		$this->index++;
	}

	public function rewind(): void {
		$this->effects = [];
		foreach (Lemuria::Score() as $effect) {
			/** @var Effect $effect */
			if ($effect->supportsSimulation()) {
				if (!$this->party || $this->filterByParty($effect)) {
					$this->effects[] = $effect;
				}
			}
		}
		$this->index = 0;
		$this->count = count($this->effects);
	}

	public function valid(): bool {
		return $this->index < $this->count;
	}

	public function find(Identifiable $effect): ?Identifiable {
		return Lemuria::Score()->find($effect);
	}

	/**
	 * @return array<Identifiable>
	 */
	public function findAll(Identifiable $entity): array {
		return Lemuria::Score()->findAll($entity);
	}

	public function add(Identifiable $effect): static {
		Lemuria::Score()->add($effect);
		return $this;
	}

	public function remove(Identifiable $effect): static {
		Lemuria::Score()->remove($effect);
		return $this;
	}

	public function load(): static {
		Lemuria::Score()->load();
		return $this;
	}

	public function save(): static {
		Lemuria::Score()->save();
		return $this;
	}

	private function filterByParty(Effect $effect): bool {
		return match(true) {
			$effect instanceof AbstractPartyEffect => $effect->Party()         === $this->party,
			$effect instanceof AbstractUnitEffect  => $effect->Unit()->Party() === $this->party,
			default => true
		};
	}
}
