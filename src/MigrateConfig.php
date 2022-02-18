<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Model\Game;

final class MigrateConfig extends AlphaConfig
{
	public function Game(): Game {
		return new MigrateGame($this);
	}
}
