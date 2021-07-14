<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Log;

final class AlphaConfig extends LemuriaConfig
{
	public const DEBUG_PARTIES = 'debugParties';

	public const CREATE_ARCHIVES = 'createArchives';

	public const THROW_EXCEPTIONS = 'throwExceptions';

	private const DEBUG_PARTIES_DEFAULT = [];

	private const CREATE_ARCHIVES_DEFAULT = true;

	private const THROW_EXCEPTIONS_DEFAULT = false;

	protected function initDefaults(): void {
		parent::initDefaults();
		$this->defaults[self::DEBUG_PARTIES]    = self::DEBUG_PARTIES_DEFAULT;
		$this->defaults[self::CREATE_ARCHIVES]  = self::CREATE_ARCHIVES_DEFAULT;
		$this->defaults[self::THROW_EXCEPTIONS] = self::THROW_EXCEPTIONS_DEFAULT;
	}

	protected function createLog(string $logPath): Log {
		$addErrorHandler = !$this[self::THROW_EXCEPTIONS];
		return new AlphaLog($logPath, $addErrorHandler);
	}
}
