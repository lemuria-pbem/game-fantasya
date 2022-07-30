<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Monolog\Level;

use Lemuria\Engine\Fantasya\LemuriaLog;

final class FantasyaLog extends LemuriaLog
{
	protected ?Level $consoleLevel = Level::Alert;

	protected ?Level $fileLevel = Level::Debug;
}
