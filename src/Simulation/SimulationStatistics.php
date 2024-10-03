<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Simulation;

use Lemuria\Statistics\Fantasya\LemuriaStatistics;
use Lemuria\Lemuria;

final class SimulationStatistics extends LemuriaStatistics
{
	public function load(): void {
		Lemuria::Log()->debug('Loading statistics skipped in simulation.');
	}
}