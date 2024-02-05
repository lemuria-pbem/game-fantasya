<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Scenario\Fantasya\Storage\ScenarioGame;

class MigrateGame extends ScenarioGame
{
	protected function getSaveStorage(): array {
		return $this->getLoadStorage();
	}
}
