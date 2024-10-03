<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Simulation;

use Lemuria\Engine\Hostilities;
use Lemuria\Engine\Report;
use Lemuria\Game\Fantasya\FantasyaConfig;
use Lemuria\Scenario\Scripts;
use Lemuria\Statistics;

class SimulationConfig extends FantasyaConfig
{
	public function Hostilities(): Hostilities {
		return new SimulationHostilities();
	}

	public function Report(): Report {
		return new SimulationReport();
	}

	public function Scripts(): Scripts {
		return new SimulationScripts($this->Options());
	}

	public function Statistics(): Statistics {
		return new SimulationStatistics();
	}
}
