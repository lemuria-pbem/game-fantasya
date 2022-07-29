<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Storage\LemuriaGame;

class MigrateGame extends LemuriaGame
{
	protected function getSaveStorage(): array {
		return $this->getLoadStorage();
	}
}
