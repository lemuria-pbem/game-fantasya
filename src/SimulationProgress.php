<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Event\Layabout;
use Lemuria\Engine\Fantasya\Event\Simulation\Maintenance;
use Lemuria\Engine\Fantasya\Event\Simulation\Support;
use Lemuria\Engine\Fantasya\Factory\DefaultProgress;
use Lemuria\Engine\Fantasya\State;

final class SimulationProgress extends DefaultProgress
{
	public final const array EVENTS = [Maintenance::class, Support::class, Layabout::class];

	public function __construct(State $state) {
		foreach (self::EVENTS as $event) {
			$this->add(new $event($state));
		}
	}
}
