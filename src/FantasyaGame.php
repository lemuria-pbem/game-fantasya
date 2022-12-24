<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Factory\DefaultProgress;
use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Engine\Fantasya\TurnOptions;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\EntitySet;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Gathering;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Version\Module;
use Lemuria\Version\VersionFinder;

final class FantasyaGame extends FantasyaReport
{
	private readonly int $round;

	private readonly bool $debugBattles;

	private readonly bool $throwExceptions;

	private readonly LemuriaTurn $turn;

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
		Lemuria::Log()->debug('The world has ' . count(Lemuria::Catalog()->getAll(Domain::Location)) . ' regions.');
		Lemuria::Log()->debug('Evaluating round ' . $this->nextRound . '.', ['calendar' => Lemuria::Calendar()]);
		Lemuria::Calendar()->nextRound();

		$options = new TurnOptions();
		$options->setDebugBattles($this->debugBattles);
		$options->setThrowExceptions($this->throwExceptions);
		$this->turn = new LemuriaTurn($options);

		$version                 = Lemuria::Version();
		$version[Module::Engine] = $this->turn->getVersion();
		$version[Module::Game]   = $gameVersion;

		return $this;
	}

	public function readOrders(): self {
		$dir  = $this->storage . '/orders/' . $this->round;
		$path = realpath($dir);
		if (!$path) {
			throw new DirectoryNotFoundException($dir);
		}
		$parties = glob($path . DIRECTORY_SEPARATOR . '*.order');
		Lemuria::Log()->debug('Found ' . count($parties) . ' order files.', ['orders' => $parties]);

		$gathering = new Gathering();
		foreach ($parties as $path) {
			$units = $this->turn->add(new CommandFile($path));
			$party = $this->getPartyFrom($units);
			if ($party) {
				$this->addDefaultOrders($party, $units);
				$gathering->add($party);
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

	private function getPartyFrom(EntitySet $units): ?Party {
		if ($units->count() > 0) {
			$units->rewind();
			/** @var Unit $unit */
			$unit = $units->current();
			return $unit->Party();
		}
		return null;
	}

	private function addDefaultOrders(Party $party, EntitySet $units): void {
		foreach ($party->People() as $unit /* @var Unit $unit */) {
			if (!$units->has($unit->Id())) {
				$this->turn->substitute($unit);
			}
		}
	}

	private function addMissingParties(Gathering $gathering): void {
		foreach (Lemuria::Catalog()->getAll(Domain::Party) as $party /* @var Party $party */) {
			if ($party->Type() === Type::Player && !$party->hasRetired() && !$gathering->has($party->Id())) {
				$this->turn->substitute($party);
			}
		}
	}
}
