<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Event\Layabout;
use Lemuria\Engine\Fantasya\Factory\DefaultProgress;

final class SimulationProgress extends DefaultProgress
{
	public final const EVENTS = [Layabout::class];

	/**
	 * @return array<string>
	 */
	protected function getEvents(): array {
		return self::EVENTS;
	}
}
