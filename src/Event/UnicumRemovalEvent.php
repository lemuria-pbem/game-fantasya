<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Event;

use Lemuria\Engine\Fantasya\Effect\UnicumRemoval;
use Lemuria\Engine\Fantasya\Event\AbstractEvent;
use Lemuria\Engine\Fantasya\Priority;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Unicum;

final class UnicumRemovalEvent extends AbstractEvent
{
	public function __construct(State $state) {
		parent::__construct($state, Priority::Before);
	}

	protected function run(): void {
		$score  = Lemuria::Score();
		$effect = new UnicumRemoval($this->state);
		foreach (Unicum::all() as $unicum) {
			/** @var UnicumRemoval|null $removal */
			$removal = $score->find($effect->setUnicum($unicum));
			$removal?->prepare()->execute();
		}
	}
}
