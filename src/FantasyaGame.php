<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Factory\DefaultProgress;
use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
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
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Version\Module;
use Lemuria\Version\VersionFinder;

class FantasyaGame extends FantasyaReport
{
	protected readonly LemuriaTurn $turn;

	private readonly int $round;

	private readonly bool $debugBattles;

	private readonly bool $throwExceptions;

	public function __construct() {
		parent::__construct();
		$this->round           = $this->nextRound++;
		$this->debugBattles    = $this->config[FantasyaConfig::DEBUG_BATTLES];
		$this->throwExceptions = $this->config[FantasyaConfig::THROW_EXCEPTIONS];
	}

	public function Round(): int {
		return $this->round;
	}

	public function init(): self {
		$versionFinder = new VersionFinder(__DIR__ . '/..');
		$gameVersion   = $versionFinder->get();

		Lemuria::init($this->config);
		Lemuria::Log()->debug('Turn starts (' . $gameVersion . ').', ['config' => $this->config]);
		Lemuria::load();
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
			$units = $this->turn->add(new CommandFile($path));
			$party = $this->getPartyFrom($units);
			if ($party) {
				$this->addDefaultOrders($party, $units);
				$gathering->add($party);
				$this->received[$party->Id()->Id()] = filemtime($path);
			}
		}
		$this->addMissingParties($gathering);

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
		return $this;
	}

	public function evaluate(): self {
		Lemuria::Log()->debug('Add effects and events.');
		$this->turn->addScore(Lemuria::Score())->addProgress(new DefaultProgress(State::getInstance()));
		Lemuria::Log()->debug('Starting evaluation.');
		$this->turn->evaluate();

		return $this;
	}

	public function finish(): self {
		$this->turn->prepareNext();
		Lemuria::save();
		$this->config[LemuriaConfig::ROUND] = $this->nextRound;
		$this->config[LemuriaConfig::MDD]   = time();
		Lemuria::Log()->debug('Turn ended.');

		return $this;
	}

	public function shutdown(): self {
		/** @var FantasyaNamer $namer */
		$namer = $this->config->Namer();
		$namer->updateNameLists();

		return $this;
	}

	public function archiveLog(): self {
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
		if ($this->throwExceptions) {
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
		return $options->setDebugBattles($this->debugBattles)->setThrowExceptions($this->throwExceptions);
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

	private function getPartyFrom(EntitySet $units): ?Party {
		if ($units->count() > 0) {
			$units->rewind();
			/** @var Unit $unit */
			$unit = $units->current();
			return $unit->Party();
		}
		return null;
	}
}
