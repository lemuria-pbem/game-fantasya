<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Turn\Options;
use Lemuria\Engine\Fantasya\Turn\SelectiveCherryPicker;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Gathering;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;

class FantasyaDebugger extends FantasyaGame
{
	private SelectiveCherryPicker $cherryPicker;

	public function __construct() {
		parent::__construct();
		$this->cherryPicker = new SelectiveCherryPicker();
	}

	public function CherryPicker(): SelectiveCherryPicker {
		return $this->cherryPicker;
	}

	public function init(): static {
		parent::init();
		Lemuria::Log()->debug('We are in debugging mode using SelectiveCherryPicker.');
		if (function_exists('modifyAnythingForDebugging')) {
			modifyAnythingForDebugging();
		}
		return $this;
	}

	protected function generateReportFor(Party $party): bool {
		if (parent::generateReportFor($party)) {
			return $this->cherryPicker->pickParty($party);
		}
		return false;
	}

	protected function createOptions(): Options {
		$options = parent::createOptions();
		$options->setCherryPicker($this->cherryPicker);
		return $options;
	}

	protected function findOrderFiles(): array {
		$files = [];
		foreach (parent::findOrderFiles() as $path) {
			$file = basename($path);
			$uuid = substr($file, 0, strpos($file, '.'));
			if ($this->cherryPicker->pickParty($uuid)) {
				$files[] = $path;
			}
		}
		return $files;
	}

	protected function addMissingParties(Gathering $gathering): void {
		foreach (Party::all() as $party) {
			if ($party->Type() === Type::Player && !$party->hasRetired() && !$gathering->has($party->Id())) {
				if ($this->cherryPicker->pickParty($party)) {
					$this->turn->substitute($party);
					$this->received[$party->Id()->Id()] = 0;
				}
			}
		}
	}
}
