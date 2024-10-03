<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Simulation;

use Lemuria\Engine\Fantasya\LemuriaReport;
use Lemuria\Lemuria;

final class SimulationReport extends LemuriaReport
{
	public function load(): static {
		Lemuria::Log()->debug('Loading report skipped in simulation.');
		return $this;
	}
}
