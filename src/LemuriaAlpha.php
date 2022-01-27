<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Engine\Fantasya\Factory\DefaultProgress;
use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Engine\Fantasya\TurnOptions;
use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\DebugFilter;
use Lemuria\Engine\Message\Filter\NullFilter;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\EntitySet;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Gathering;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Renderer\Text\BattleLogWriter;
use Lemuria\Renderer\Text\HtmlWriter;
use Lemuria\Renderer\Text\OrderWriter;
use Lemuria\Renderer\Text\SpellBookWriter;
use Lemuria\Renderer\Text\TextWriter;
use Lemuria\Renderer\Text\Wrapper\FileWrapper;
use Lemuria\Version;
use Lemuria\Version\VersionFinder;

final class LemuriaAlpha
{
	private const HTML_WRAPPER = __DIR__ . '/../resources/turn.html';

	private const ZIP_OPTIONS = ['remove_all_path' => true];

	private readonly AlphaConfig $config;

	private readonly int $round;

	private readonly int $nextRound;

	private readonly bool $debugBattles;

	private readonly array $debugParties;

	private readonly bool $createArchives;

	private readonly bool $throwExceptions;

	private readonly string $storage;

	private readonly LemuriaTurn $turn;

	public function __construct() {
		$this->storage = realpath(__DIR__ . '/../storage');
		if (!$this->storage) {
			throw new DirectoryNotFoundException($this->storage);
		}
		$this->config          = new AlphaConfig($this->storage);
		$this->round           = $this->config[LemuriaConfig::ROUND];
		$this->nextRound       = $this->round + 1;
		$this->debugBattles    = $this->config[AlphaConfig::DEBUG_BATTLES];
		$this->debugParties    = array_fill_keys($this->config[AlphaConfig::DEBUG_PARTIES], true);
		$this->createArchives  = $this->config[AlphaConfig::CREATE_ARCHIVES];
		$this->throwExceptions = $this->config[AlphaConfig::THROW_EXCEPTIONS];
	}

	public function Round(): int {
		return $this->round;
	}

	public function init(): self {
		Lemuria::init($this->config);
		Lemuria::Log()->debug('Turn starts.', ['config' => $this->config]);
		Lemuria::load();
		Lemuria::Log()->debug('Evaluating round ' . $this->nextRound . '.', ['calendar' => Lemuria::Calendar()]);
		Lemuria::Calendar()->nextRound();

		$options = new TurnOptions();
		$options->setDebugBattles($this->debugBattles);
		$options->setThrowExceptions($this->throwExceptions);
		$this->turn = new LemuriaTurn($options);

		$version                  = Lemuria::Version();
		$version[Version::ENGINE] = $this->turn->getVersion();
		$versionFinder            = new VersionFinder(__DIR__ . '/..');
		$version[Version::GAME]   = $versionFinder->get();

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
		Lemuria::save();
		$this->config[LemuriaConfig::ROUND] = $this->nextRound;
		$this->config[LemuriaConfig::MDD]   = time();
		Lemuria::Log()->debug('Turn ended.');

		return $this;
	}

	public function createReports(): self {
		Lemuria::Log()->debug('Generating reports.');
		$dir = realpath($this->storage . '/turn');
		if (!$dir) {
			throw new DirectoryNotFoundException($dir);
		}
		$dir .= DIRECTORY_SEPARATOR . $this->nextRound;
		if (!is_dir($dir)) {
			mkdir($dir);
			chmod($dir, 0775);
		}
		$version = Lemuria::Version();

		$p          = 0;
		$hasVersion = false;
		foreach (Lemuria::Catalog()->getAll(Domain::PARTY) as $party /* @var Party $party */) {
			$id       = $party->Id();
			$isPlayer = $party->Type() === Type::PLAYER;
			$name     = (string)$id;
			$filter   = $this->getMessageFilter($party);
			Lemuria::Log()->debug('Using ' . get_class($filter) . ' for report messages of Party ' . $id . '.');

			$crPath = $dir . DIRECTORY_SEPARATOR . $name . '.cr';
			$writer = new MagellanWriter($crPath);
			if (!$hasVersion) {
				$version[Version::RENDERERS] = $writer->getVersion();
			}
			if ($isPlayer) {
				$writer->setFilter($filter)->render($id);
			}

			$htmlPath = $dir . DIRECTORY_SEPARATOR . $name . '.html';
			$writer   = new HtmlWriter($htmlPath);
			if (!$hasVersion) {
				$version[Version::RENDERERS] = $writer->getVersion();
			}
			$writer->add(new FileWrapper(self::HTML_WRAPPER))->setFilter($filter)->render($id);

			if ($isPlayer) {
				$txtPath = $dir . DIRECTORY_SEPARATOR . $name . '.txt';
				$writer  = new TextWriter($txtPath);
				$writer->setFilter($filter)->render($id);

				$orderPath = $dir . DIRECTORY_SEPARATOR . $name . '.orders.txt';
				$writer    = new OrderWriter($orderPath);
				$writer->render($id);

				if ($party->SpellBook()->count() > 0) {
					$orderPath = $dir . DIRECTORY_SEPARATOR . $name . '.spells.txt';
					$writer    = new SpellBookWriter($orderPath);
					$writer->render($id);
				}

				$suffix = '.battle.' . BattleLogWriter::LOCATION_PLACEHOLDER . '.txt';
				$writer = new BattleLogWriter($dir . DIRECTORY_SEPARATOR . $name . $suffix);
				$writer->render($id);
			}

			$p++;
			$hasVersion = true;
		}
		Lemuria::Log()->debug('Report generation finished for ' . $p . ' parties.');

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function createArchives(): array {
		if (!$this->createArchives) {
			Lemuria::Log()->debug('Generating ZIP files has been disabled.');
			return [];
		}

		Lemuria::Log()->debug('Generating ZIP files.');
		$turnDir = realpath($this->storage . '/turn/' . $this->nextRound);
		if (!$turnDir) {
			throw new DirectoryNotFoundException($turnDir);
		}
		$reportDir = realpath($this->storage . '/report');
		if (!$reportDir) {
			throw new DirectoryNotFoundException($reportDir);
		}
		$reportDir .= DIRECTORY_SEPARATOR . $this->nextRound;
		if (!is_dir($reportDir)) {
			mkdir($reportDir);
			chmod($reportDir, 0775);
		}

		$archives = [];
		foreach (Lemuria::Catalog()->getAll(Domain::PARTY) as $party /* @var Party $party */) {
			if ($party->Type() !== Type::PLAYER) {
				continue;
			}

			$id       = (string)$party->Id();
			$name     = $this->nextRound . '-' . $id . '.zip';
			$zipPath  = $reportDir . DIRECTORY_SEPARATOR . $name;
			$turnPath = $turnDir . DIRECTORY_SEPARATOR . $id . '.*';

			$zip    = new \ZipArchive();
			$result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
			if (is_int($result)) {
				throw new \RuntimeException('Could not create ZIP file.', $result);
			}
			$result = $zip->addGlob($turnPath, 0, self::ZIP_OPTIONS);
			if (empty($result)) {
				throw new \RuntimeException('No files were added to ZIP for party ' . $id . '.');
			}
			$result = $zip->close();
			if (!$result) {
				throw new \RuntimeException('Error on closing ZIP.');
			}
			$archives[] = $id . ':' . $party->Uuid() . ':' . $zipPath;
		}

		return $archives;
	}

	public function archiveLog(): self {
		Lemuria::Log()->debug('Archiving log file.');
		$logDir = realpath($this->storage . '/' . LemuriaConfig::LOG_DIR);
		if (!$logDir) {
			throw new DirectoryNotFoundException($logDir);
		}
		$destinationDir = $logDir . DIRECTORY_SEPARATOR . $this->round;
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

	private function getMessageFilter(Party $party): Filter {
		$id = $party->Uuid();
		return isset($this->debugParties[$id]) ? new NullFilter() : new DebugFilter();
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
		foreach (Lemuria::Catalog()->getAll(Domain::PARTY) as $party /* @var Party $party */) {
			if ($party->Type() === Type::PLAYER && !$gathering->has($party->Id())) {
				$this->turn->substitute($party);
			}
		}
	}
}
