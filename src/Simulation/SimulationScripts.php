<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Simulation;

use Lemuria\Lemuria;
use Lemuria\Scenario\Fantasya\LemuriaScripts;

final class SimulationScripts extends LemuriaScripts
{
	public function load(): static {
		Lemuria::Log()->debug('Loading scripts skipped in simulation.');
		return $this;
	}
}