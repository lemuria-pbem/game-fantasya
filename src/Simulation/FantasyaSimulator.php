<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Simulation;

use Lemuria\Cache\FastCache;
use Lemuria\Dispatcher\Event\FastCache\Persisting;
use Lemuria\Dispatcher\Event\FastCache\Restored;
use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\Message\LemuriaMessage;
use Lemuria\Engine\Fantasya\Message\Reliability;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Engine\Fantasya\Turn\Option\ThrowOption;
use Lemuria\Engine\Fantasya\Turn\Options;
use Lemuria\Engine\Fantasya\Turn\SelectiveCherryPicker;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter\DebugFilter;
use Lemuria\Engine\Message\Result;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Game\Fantasya\FantasyaConfig;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party\Census;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Profiler;

final class FantasyaSimulator
{
	/**
	 * @type array<string, string>
	 */
	private const array LEVEL = [Result::Error->value => 'F', Result::Event->value => 'E', Result::Failure->value => '!', Result::Success->value => ' '];

	private const string UNDETERMINED = 'S';

	private const string CACHE_DIRECTORY = __DIR__ . '/../../storage/cache';

	private const string LOG_FILE = 'simulation.log';

	private static ?self $instance = null;

	private readonly SimulationConfig $config;

	private readonly Options $options;

	private readonly bool $profilingEnabled;

	private ?Party $party = null;

	public static function boot(BootOption $option = BootOption::NoCache): self|bool|null {
		switch ($option) {
			case BootOption::BuildCache :
				self::$instance = new self();
				Lemuria::Register()->addListener(new Persisting(new FastCache()), self::onPersisting(...));
				Lemuria::storeTo(self::CACHE_DIRECTORY, __CLASS__);
				return null;
			case BootOption::ClearCache :
				return FastCache::delete(self::CACHE_DIRECTORY, __CLASS__);
			/** @noinspection PhpMissingBreakStatementInspection */
			case BootOption::FromCache :
				Lemuria::boot();
				Lemuria::Register()->addListener(new Restored(new FastCache()), self::onRestored(...));
				Lemuria::restoreFrom(self::CACHE_DIRECTORY, __CLASS__);
				if (self::$instance) {
					return self::$instance;
				}
				// There is no break here intentionally!
			default :
				self::$instance = new self();
				return self::$instance;
		}
	}

	public function __construct() {
		$storage = realpath(__DIR__ . '/../../storage');
		if (!$storage) {
			throw new DirectoryNotFoundException($storage);
		}

		$this->config           = new SimulationConfig($storage);
		$this->profilingEnabled = $this->config[FantasyaConfig::ENABLE_PROFILING];

		Lemuria::init($this->config->setLogFile(self::LOG_FILE));
		Lemuria::Profiler()->setEnabled($this->profilingEnabled);
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->logRecord([Profiler::RECORD_ZERO, Profiler::RECORD_BUILDER]);
		}

		Lemuria::Log()->debug('Loading Lemuria.', ['storage' => $storage]);
		$this->options = $this->createOptions();
		Lemuria::load();
	}

	public function pick(Party $party): FantasyaSimulator {
		$this->party = $party;
		return $this;
	}

	public function simulate(CommandFile $move): FantasyaSimulator {
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaSimulator_init');
		}
		Lemuria::Log()->debug('Simulating move.', ['move' => $move]);
		Lemuria::Calendar()->nextRound();
		$turn = new LemuriaTurn($this->options);
		$turn->add($move);
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaSimulator_add');
		}
		$turn->addScore(new SimulationScore($this->party));
		$turn->addProgress(new SimulationProgress(State::getInstance()))->evaluate();
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaSimulator_simulate');
		}
		return $this;
	}

	public function Round(): int {
		return $this->config[LemuriaConfig::ROUND];
	}

	/**
	 * @return array<string, array<Message>>
	 */
	public function getReport(Party $party): array {
		Lemuria::Log()->debug('Getting messages.');
		$filter   = new DebugFilter();
		$messages = [];

		foreach (Lemuria::Report()->getAll($party) as $message) {
			if (!$filter->retains($message)) {
				$messages[''][] = $message;
			}
		}

		$census = new Census($party);
		foreach ($census->getAtlas() as $region) {
			foreach ($census->getPeople($region) as $unit) {
				$index = (string)$unit;
				foreach (Lemuria::Report()->getAll($unit) as $message) {
					if (!$filter->retains($message)) {
						$messages[$index][] = $message;
					}
				}
			}
		}

		return $messages;
	}

	public function render(LemuriaMessage $message): string {
		$level = self::LEVEL[$message->Result()->value];
		if ($message->MessageType()->Reliability() !== Reliability::Determined) {
			$level = self::UNDETERMINED;
		}
		return '[' . $level . '] ' . $message;
	}

	public function logProfiler(): void {
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaSimulator_finished');
			Lemuria::Profiler()->recordTotal();
			Lemuria::Profiler()->logTotalPeak();
		}
	}

	protected function createOptions(): Options {
		$options = $this->config->Options();
		$options->setThrowExceptions(new ThrowOption('NONE'))->setIsSimulation(true);
		$options->setIsProfiling($this->profilingEnabled);
		if ($this->party) {
			$cherryPicker = new SelectiveCherryPicker();
			$options->setCherryPicker($cherryPicker->add($this->party));
		}
		return $options;
	}

	private static function onRestored(Restored $event): void {
		self::$instance = $event->cache->get(__CLASS__);
	}

	private static function onPersisting(Persisting $event): void {
		$event->cache->set(self::$instance);
	}
}