<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Monolog\Logger;

use Lemuria\Engine\Fantasya\LemuriaLog;

final class AlphaLog extends LemuriaLog
{
	protected ?int $consoleLevel = Logger::ERROR;

	protected ?int $fileLevel = Logger::DEBUG;
}
