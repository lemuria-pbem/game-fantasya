<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Factory\DefaultProgress;
use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Engine\Fantasya\Turn\Option\ThrowOption;
use Lemuria\Engine\Fantasya\Turn\Options;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\EntitySet;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Game\Fantasya\Factory\FantasyaNamer;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Gathering;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Profiler;
use Lemuria\Version\Module;
use Lemuria\Version\VersionFinder;

class FantasyaGame extends FantasyaReport
{
	protected readonly LemuriaTurn $turn;

	private readonly int $round;

	private readonly bool $debugBattles;

	private readonly ThrowOption $throwExceptions;

	public function __construct() {
		parent::__construct();
		$this->round            = $this->nextRound++;
		$this->debugBattles     = $this->config[FantasyaConfig::DEBUG_BATTLES];
		$this->throwExceptions  = new ThrowOption($this->config[FantasyaConfig::THROW_EXCEPTIONS]);
	}

	public function Round(): int {
		return $this->round;
	}

	public function init(): self {
		$versionFinder = new VersionFinder(__DIR__ . '/..');
		$gameVersion   = $versionFinder->get();

		Lemuria::init($this->config);
		Lemuria::Log()->debug('Turn starts (' . $gameVersion . ').', ['config' => $this->config]);
		if ($this->profilingEnabled) {
			Lemuria::Log()->debug('Profiler [' . Profiler::RECORD_ZERO . ']: ' . Lemuria::Profiler()->getRecord(Profiler::RECORD_ZERO));
		}
		Lemuria::load();
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaGame_load');
		}
		Lemuria::Log()->debug('The world has ' . count(Region::all()) . ' regions.');
		Lemuria::Log()->debug('Evaluating round ' . $this->nextRound . '.', ['calendar' => Lemuria::Calendar()]);
		Lemuria::Calendar()->nextRound();

		$options    = $this->createOptions();
		$this->turn = new LemuriaTurn($options);

		$version                 = Lemuria::Version();
		$version[Module::Engine] = $this->turn->getVersion();
		$version[Module::Game]   = $gameVersion;

		return $this;
	}

	public function readOrders(): self {
		$files = $this->findOrderFiles();
		Lemuria::Log()->debug('Found ' . count($files) . ' order files.', ['orders' => $files]);

		$gathering = new Gathering();
		foreach ($files as $path) {
			$this->turn->add(new CommandFile($path));
			$result = $this->turn->getResult();
			$party  = $result->Party();
			$units  = $result->Units();
			if ($party) {
				$this->addDefaultOrders($party, $units);
				$gathering->add($party);
				$this->received[$party->Id()->Id()] = filemtime($path);
			}
		}
		$this->addMissingParties($gathering);

		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaGame_read');
		}
		return $this;
	}

	public function initiate(): self {
		$n = Lemuria::Debut()->count();
		if ($n > 0) {
			Lemuria::Log()->debug('Initiate ' . $n . ' newcomers.');
			foreach (Lemuria::Debut()->getAll() as $newcomer) {
				$this->turn->initiate($newcomer);
			}
			Lemuria::Debut()->clear();
		} else {
			Lemuria::Log()->debug('No newcomers to initiate.');
		}

		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaGame_initiate');
		}
		return $this;
	}

	public function evaluate(): self {
		Lemuria::Log()->debug('Add effects and events.');
		$this->turn->addScore(Lemuria::Score())->addProgress(new DefaultProgress(State::getInstance()));
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaGame_progress');
		}
		Lemuria::Log()->debug('Starting evaluation.');
		$this->turn->evaluate();

		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaGame_evaluate');
		}
		return $this;
	}

	public function finish(): self {
		$this->turn->prepareNext();
		Lemuria::save();
		$this->config[LemuriaConfig::ROUND] = $this->nextRound;
		$this->config[LemuriaConfig::MDD]   = time();
		Lemuria::Log()->debug('Turn ended.');

		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaGame_finish');
		}
		return $this;
	}

	public function shutdown(): self {
		/** @var FantasyaNamer $namer */
		$namer = $this->config->Namer();
		$namer->updateNameLists();

		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaGame_shutdown');
		}
		return $this;
	}

	public function archiveLog(): self {
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordTotal();
			Lemuria::Profiler()->logTotalPeak();
		}

		Lemuria::Log()->debug('Archiving log file.');
		$logDir = realpath($this->storage . '/' . LemuriaConfig::LOG_DIR);
		if (!$logDir) {
			throw new DirectoryNotFoundException($logDir);
		}
		$destinationDir = $logDir . DIRECTORY_SEPARATOR . $this->nextRound;
		if (!is_dir($destinationDir)) {
			mkdir($destinationDir);
			chmod($destinationDir, 0775);
		}
		$source      = $logDir . DIRECTORY_SEPARATOR . LemuriaConfig::LOG_FILE;
		$destination = $destinationDir . DIRECTORY_SEPARATOR . LemuriaConfig::LOG_FILE;
		if (!rename($source, $destination)) {
			throw new \RuntimeException('Could not archive log file.');
		}

		return $this;
	}

	public function logException(\Throwable $throwable): self {
		if ($this->throwExceptions[ThrowOption::ANY]) {
			throw $throwable;
		}
		try {
			Lemuria::Log()->critical($throwable->getMessage(), ['exception' => $throwable]);
		} catch (\Throwable) {
			throw $throwable;
		}

		return $this;
	}

	public function getReports(): array {
		$dir = realpath($this->storage . '/turn/' . $this->nextRound);
		if (!$dir) {
			throw new DirectoryNotFoundException($dir);
		}
		return glob($dir . DIRECTORY_SEPARATOR . '*.html');
	}

	protected function createOptions(): Options {
		$options = new Options();
		return $options->setDebugBattles($this->debugBattles)->setThrowExceptions($this->throwExceptions)->setIsProfiling($this->profilingEnabled);
	}

	protected function findOrderFiles(): array {
		$dir  = $this->storage . '/orders/' . $this->round;
		$path = realpath($dir);
		if (!$path) {
			throw new DirectoryNotFoundException($dir);
		}
		return glob($path . DIRECTORY_SEPARATOR . '*.order');
	}

	protected function addDefaultOrders(Party $party, EntitySet $units): void {
		foreach ($party->People() as $unit) {
			if (!$units->has($unit->Id())) {
				$this->turn->substitute($unit);
			}
		}
	}

	protected function addMissingParties(Gathering $gathering): void {
		foreach (Party::all() as $party) {
			if ($party->Type() === Type::Player && !$party->hasRetired() && !$gathering->has($party->Id())) {
				$this->turn->substitute($party);
				$this->received[$party->Id()->Id()] = 0;
			}
		}
	}
}
