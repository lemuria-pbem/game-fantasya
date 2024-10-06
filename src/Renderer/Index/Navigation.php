<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer\Index;

readonly class Navigation
{
	public function __construct(private int $currentRound, private int $timestampNext, private int $firstRound = 1) {
	}

	/**
	 * The number of the previous round before the current one.
	 *
	 * There may be no previous round if the current round is the first with
	 * the administrator overview feature.
	 */
	public function Previous(): ?int {
		return $this->currentRound > $this->firstRound ? $this->currentRound - 1 : null;
	}

	/**
	 * The number of the current round.
	 */
	public function Round(): int {
		return $this->currentRound;
	}

	/**
	 * The number of the next round after the current one.
	 */
	public function Next(): int {
		return $this->currentRound + 1;
	}

	/**
	 * The timestamp of the availability of next report.
	 */
	public function NextAt(): int {
		return $this->timestampNext;
	}
}
