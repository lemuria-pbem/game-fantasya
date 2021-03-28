<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Engine\Fantasya\Factory\DefaultProgress;
use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\DebugFilter;
use Lemuria\Engine\Message\Filter\NullFilter;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\EntitySet;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Fantasya\Gathering;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Renderer\Text\HtmlWriter;
use Lemuria\Renderer\Text\OrderWriter;
use Lemuria\Renderer\Text\TextWriter;
use Lemuria\Renderer\Text\Wrapper\FileWrapper;

final class LemuriaAlpha
{
	private const HTML_WRAPPER = __DIR__ . '/../resources/turn.html';

	private const ZIP_OPTIONS = ['remove_all_path' => true];

	private AlphaConfig $config;

	private int $round;

	private int $nextRound;

	private array $debugParties;

	private bool $throwExceptions;

	private string $storage;

	private LemuriaTurn $turn;

	public function __construct() {
		$this->storage = realpath(__DIR__ . '/../storage');
		if (!$this->storage) {
			throw new DirectoryNotFoundException($this->storage);
		}
		$this->config          = new AlphaConfig($this->storage);
		$this->round           = $this->config[AlphaConfig::ROUND];
		$this->nextRound       = $this->round;
		$this->debugParties    = array_fill_keys($this->config[AlphaConfig::DEBUG_PARTIES], true);
		$this->throwExceptions = $this->config[AlphaConfig::THROW_EXCEPTIONS];
	}

	public function Round(): int {
		return $this->round;
	}

	public function init(): self {
		Lemuria::init($this->config);
		Lemuria::Log()->debug('Turn starts.', ['config' => $this->config]);
		Lemuria::load();
		Lemuria::Log()->debug('Evaluating round ' . Lemuria::Calendar()->Round() . '.', ['calendar' => Lemuria::Calendar()]);
		$this->turn = new LemuriaTurn($this->throwExceptions);

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

	public function evaluate(): self {
		Lemuria::Log()->debug('Add effects and events.');
		$this->turn->addScore(Lemuria::Score())->addProgress(new DefaultProgress(State::getInstance()));
		Lemuria::Log()->debug('Starting evaluation.');
		$this->turn->evaluate();
		Lemuria::Calendar()->nextRound();

		return $this;
	}

	public function finish(): self {
		Lemuria::save();
		$this->nextRound                    = $this->round + 1;
		$this->config[AlphaConfig::ROUND] = $this->nextRound;
		$this->config[AlphaConfig::MDD]   = time();
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
		$p = 0;
		foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
			$id     = $party->Id();
			$name   = (string)$id;
			$filter = $this->getMessageFilter($party);
			Lemuria::Log()->debug('Using ' . get_class($filter) . ' for report messages of Party ' . $id . '.');

			$htmlPath = $dir . DIRECTORY_SEPARATOR . $name . '.html';
			$writer   = new HtmlWriter($htmlPath);
			$writer->add(new FileWrapper(self::HTML_WRAPPER))->setFilter($filter)->render($id);

			$txtPath = $dir . DIRECTORY_SEPARATOR . $name . '.txt';
			$writer  = new TextWriter($txtPath);
			$writer->setFilter($filter)->render($id);

			$crPath = $dir . DIRECTORY_SEPARATOR . $name . '.cr';
			$writer = new MagellanWriter($crPath);
			$writer->setFilter($filter)->render($id);

			$orderPath = $dir . DIRECTORY_SEPARATOR . $name . '.orders.txt';
			$writer = new OrderWriter($orderPath);
			$writer->setFilter($filter)->render($id);

			$p++;
		}
		Lemuria::Log()->debug('Report generation finished for ' . $p . ' parties.');

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function createArchives(): array {
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
		foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
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
		$logDir = realpath($this->storage . '/' . AlphaConfig::LOG_DIR);
		if (!$logDir) {
			throw new DirectoryNotFoundException($logDir);
		}
		$destinationDir = $logDir . DIRECTORY_SEPARATOR . $this->round;
		if (!is_dir($destinationDir)) {
			mkdir($destinationDir);
			chmod($destinationDir, 0775);
		}
		$source      = $logDir . DIRECTORY_SEPARATOR . AlphaConfig::LOG_FILE;
		$destination = $destinationDir . DIRECTORY_SEPARATOR . AlphaConfig::LOG_FILE;
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
		foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
			if (!$gathering->has($party->Id())) {
				$this->turn->substitute($party);
			}
		}
	}
}
