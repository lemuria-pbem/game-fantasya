<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Log;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Storage\JsonProvider;

class AlphaConfig extends LemuriaConfig
{
	public final const DEBUG_BATTLES = 'debugBattles';

	public final const DEBUG_PARTIES = 'debugParties';

	public final const CREATE_ARCHIVES = 'createArchives';

	public final const THROW_EXCEPTIONS = 'throwExceptions';

	protected final const LOCAL_CONFIG = 'config.local.json';

	private const DEBUG_BATTLES_DEFAULT = false;

	private const DEBUG_PARTIES_DEFAULT = [];

	private const CREATE_ARCHIVES_DEFAULT = true;

	private const THROW_EXCEPTIONS_DEFAULT = false;

	/**
	 * @throws JsonException
	 */
	public function __construct(string $storagePath) {
		parent::__construct($storagePath);
		$this->overrideWithLocalConfig($storagePath);
	}

	protected function initDefaults(): void {
		parent::initDefaults();
		$this->defaults[self::DEBUG_BATTLES]    = self::DEBUG_BATTLES_DEFAULT;
		$this->defaults[self::DEBUG_PARTIES]    = self::DEBUG_PARTIES_DEFAULT;
		$this->defaults[self::CREATE_ARCHIVES]  = self::CREATE_ARCHIVES_DEFAULT;
		$this->defaults[self::THROW_EXCEPTIONS] = self::THROW_EXCEPTIONS_DEFAULT;
	}

	protected function createLog(string $logPath): Log {
		$addErrorHandler = !$this[self::THROW_EXCEPTIONS];
		return new AlphaLog($logPath, $addErrorHandler);
	}

	protected function overrideWithLocalConfig(string $storagePath): void {
		$file = new JsonProvider($storagePath);
		if ($file->exists(self::LOCAL_CONFIG)) {
			foreach ($file->read(self::LOCAL_CONFIG) as $key => $value) {
				$this->offsetSet($key, $value);
			}
		}
	}
}
