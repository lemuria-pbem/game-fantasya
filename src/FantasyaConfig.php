<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Factory\Namer;
use Lemuria\Game\Fantasya\Factory\FantasyaNamer;
use Lemuria\Model\Fantasya\Commodity\Monster\Zombie;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Game;
use Lemuria\Scenario\Fantasya\Storage\ScenarioGame;
use Lemuria\Statistics\Fantasya\LemuriaStatistics;
use Lemuria\Log;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Storage\JsonProvider;
use Lemuria\Statistics;

class FantasyaConfig extends LemuriaConfig
{
	public final const DEVELOPMENT_MODE = 'developmentMode';

	public final const DEBUG_BATTLES = 'debugBattles';

	public final const DEBUG_PARTIES = 'debugParties';

	public final const THROW_EXCEPTIONS = 'throwExceptions';

	public final const ENABLE_PROFILING = 'enableProfiling';

	public const PARTY_BY_TYPE = [Type::NPC->value => 'n', Type::Monster->value => 'm'];

	public const PARTY_BY_RACE = [Zombie::class => 'z'];

	protected final const LOCAL_CONFIG = 'config.local.json';

	private const DEVELOPMENT_MODE_DEFAULT = false;

	private const DEBUG_BATTLES_DEFAULT = false;

	private const DEBUG_PARTIES_DEFAULT = [];

	private const THROW_EXCEPTIONS_DEFAULT = 'NONE';

	private const ENABLE_PROFILING_DEFAULT = false;

	private FantasyaNamer $namer;

	/**
	 * @throws JsonException
	 */
	public function __construct(string $storagePath) {
		parent::__construct($storagePath);
		$this->overrideWithLocalConfig($storagePath);
		$this->featureFlag->setIsDevelopment($this->offsetGet(self::DEVELOPMENT_MODE));
		$this->namer = new FantasyaNamer();
	}

	public function Game(): Game {
		return new ScenarioGame($this);
	}

	public function Statistics(): Statistics {
		return new LemuriaStatistics();
	}

	public function Namer(): Namer {
		return $this->namer;
	}

	protected function initDefaults(): void {
		parent::initDefaults();
		$this->defaults[self::DEVELOPMENT_MODE] = self::DEVELOPMENT_MODE_DEFAULT;
		$this->defaults[self::DEBUG_BATTLES]    = self::DEBUG_BATTLES_DEFAULT;
		$this->defaults[self::DEBUG_PARTIES]    = self::DEBUG_PARTIES_DEFAULT;
		$this->defaults[self::THROW_EXCEPTIONS] = self::THROW_EXCEPTIONS_DEFAULT;
		$this->defaults[self::ENABLE_PROFILING] = self::ENABLE_PROFILING_DEFAULT;
	}

	protected function createLog(string $logPath): Log {
		$addErrorHandler = !$this[self::THROW_EXCEPTIONS];
		return new FantasyaLog($logPath, $addErrorHandler);
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
