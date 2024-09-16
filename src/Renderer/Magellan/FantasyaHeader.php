<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer\Magellan;

use Lemuria\Renderer\Magellan\Header;
use Lemuria\Renderer\Magellan\MailTo;

class FantasyaHeader implements Header, MailTo
{
	private const string GAME = 'Lemuria';

	private const int MAX_UNITS = 1000;

	private const int ERA = 1;

	private const string COMMAND = 'Lemuria Befehle';

	private const string ADDRESS = 'lemuria@fantasya-pbem.de';

	public function Game(): string {
		return self::GAME;
	}

	public function MaxUnits(): int {
		return self::MAX_UNITS;
	}

	public function Era(): int {
		return self::ERA;
	}

	public function MailTo(): MailTo {
		return $this;
	}

	public function Command(): string {
		return self::COMMAND;
	}

	public function Address(): string {
		return self::ADDRESS;
	}
}
