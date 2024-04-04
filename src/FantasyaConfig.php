<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Turn\Options;
use Lemuria\Factory\Namer;
use Lemuria\Game\Fantasya\Factory\FantasyaNamer;
use Lemuria\Id;
use Lemuria\Model\Fantasya\Commodity\Monster\Zombie;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Game;
use Lemuria\Scenario\Fantasya\LemuriaScripts;
use Lemuria\Scenario\Fantasya\ScenarioConfig;
use Lemuria\Scenario\Fantasya\Storage\ScenarioGame;
use Lemuria\Scenario\Scripts;
use Lemuria\Statistics\Fantasya\LemuriaStatistics;
use Lemuria\Log;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Storage\JsonProvider;
use Lemuria\Statistics;

class FantasyaConfig extends ScenarioConfig
{
	use BuilderTrait;

	public final const string DEVELOPMENT_MODE = 'developmentMode';

	public final const string DEBUG_BATTLES = 'debugBattles';

	public final const string DEBUG_PARTIES = 'debugParties';

	public final const string THROW_EXCEPTIONS = 'throwExceptions';

	public final const string ENABLE_PROFILING = 'enableProfiling';

	protected final const string LOCAL_CONFIG = 'config.local.json';

	/**
	 * @type array<int, string>
	 */
	protected const array PARTY_BY_TYPE = [Type::NPC->value => 'n', Type::Monster->value => 'm'];

	/**
	 * @type array<string, string>
	 */
	protected const array PARTY_BY_RACE = [Zombie::class => 'z'];

	private const bool DEVELOPMENT_MODE_DEFAULT = false;

	private const bool DEBUG_BATTLES_DEFAULT = false;

	/**
	 * @type array<string>
	 */
	private const array DEBUG_PARTIES_DEFAULT = [];

	private const string THROW_EXCEPTIONS_DEFAULT = 'NONE';

	private const bool ENABLE_PROFILING_DEFAULT = false;

	private FantasyaNamer $namer;

	private ?Options $options = null;

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

	public function Scripts(): Scripts {
		return new LemuriaScripts($this->Options());
	}

	public function Options(): Options {
		if (!$this->options) {
			$this->initOptions();
		}
		return $this->options;
	}

	protected function initDefaults(): void {
		parent::initDefaults();
		$this->defaults[self::DEVELOPMENT_MODE] = self::DEVELOPMENT_MODE_DEFAULT;
		$this->defaults[self::DEBUG_BATTLES]    = self::DEBUG_BATTLES_DEFAULT;
		$this->defaults[self::DEBUG_PARTIES]    = self::DEBUG_PARTIES_DEFAULT;
		$this->defaults[self::THROW_EXCEPTIONS] = self::THROW_EXCEPTIONS_DEFAULT;
		$this->defaults[self::ENABLE_PROFILING] = self::ENABLE_PROFILING_DEFAULT;
	}

	protected function initOptions(): void {
		$this->options = new Options();
		$finder        = $this->options->Finder()->Party();
		foreach (self::PARTY_BY_TYPE as $type => $id) {
			$finder->setId(Type::from($type), Id::fromId($id));
		}
		foreach (self::PARTY_BY_RACE as $race => $id) {
			$finder->setId(self::createRace($race), Id::fromId($id));
		}
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
