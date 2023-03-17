<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer\Magellan;

use Lemuria\Renderer\Magellan\MailTo as MailToInterface;

class MailTo implements MailToInterface
{
	private const COMMAND = 'Lemuria Befehle';

	private const ADDRESS = 'lemuria@fantasya-pbem.de';

	public function Command(): string {
		return self::COMMAND;
	}

	public function Address(): string {
		return self::ADDRESS;
	}
}
