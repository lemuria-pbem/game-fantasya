<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Simulation;

use Lemuria\Engine\Fantasya\LemuriaHostilities;
use Lemuria\Lemuria;

final class SimulationHostilities extends LemuriaHostilities
{
	public function load(): static {
		Lemuria::Log()->debug('Loading hostilities skipped in simulation.');
		return $this;
	}
}