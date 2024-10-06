<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer;

use function Lemuria\getClass;
use Lemuria\Dispatcher\Attribute\Emit;
use Lemuria\Dispatcher\Event\Renderer\Written;
use Lemuria\Engine\Message\Filter;
use Lemuria\Exception\FileException;
use Lemuria\Exception\FileNotFoundException;
use Lemuria\Exception\LemuriaException;
use Lemuria\Game\Fantasya\FantasyaPathFactory;
use Lemuria\Game\Fantasya\Renderer\Index\Navigation;
use Lemuria\Game\Fantasya\Renderer\Index\ReportCollection;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Writer;
use Lemuria\Version\VersionFinder;
use Lemuria\Version\VersionTag;

class IndexWriter implements Writer
{
	public const string BODY = '%BODY%';

	/**
	 * The timestamp of the first turn with administrator overview on 2024-10-05 06:00:00.
	 */
	protected const int MINIMUM_TIMESTAMP = 1728100800;

	/**
	 * Minimum interval is one hour which may be used in a speed turn game.
	 */
	protected const int MINIMUM_INTERVAL = 3600;

	private const int DEFAULT_FIRST_ROUND = 186;

	private const int DEFAULT_FIRST_TIMESTAMP = self::MINIMUM_TIMESTAMP;

	private const int DEFAULT_INTERVAL = 7 * 24 * 3600;

	protected FantasyaPathFactory $pathFactory;

	protected View $view;

	protected readonly VersionTag $version;

	protected string $wrapper = self::BODY;

	protected int $firstRound = self::DEFAULT_FIRST_ROUND;

	protected int $turnTimestamp = self::DEFAULT_FIRST_TIMESTAMP;

	protected int $turnInterval = self::DEFAULT_INTERVAL;

	public function __construct(PathFactory $pathFactory) {
		/** @noinspection PhpConditionAlreadyCheckedInspection */
		if ($pathFactory instanceof FantasyaPathFactory) {
			$this->pathFactory = $pathFactory;
		} else {
			throw new LemuriaException('This writer needs the ' . getClass(FantasyaPathFactory::class) . '.');
		}
		$this->view    = new View($this->createNavigation());
		$versionFinder = new VersionFinder(__DIR__ . '/../..');
		$this->version = $versionFinder->get();
	}

	public function setFilter(Filter $filter): static {
		return $this;
	}

	#[Emit(Written::class)]
	public function render(Id $entity = null): static {
		$report = $this->view->generate();
		$report = str_replace(self::BODY, $report, $this->wrapper);
		$path   = $this->pathFactory->getPath($this);
		if (!file_put_contents($path, $report)) {
			throw new \RuntimeException('Could not create report in ' . $path . '.');
		}
		Lemuria::Dispatcher()->dispatch(new Written($this, $entity ?? new Id(0), $path));

		return $this;
	}

	public function getVersion(): VersionTag {
		return $this->version;
	}

	public function setTurnTime(int $firstRound, int $firstTimestamp, int $turnInterval = self::DEFAULT_INTERVAL): void {
		$this->firstRound    = $firstRound;
		$this->turnTimestamp = $firstTimestamp;
		$this->turnInterval  = $turnInterval;
	}

	public function setReportCollection(ReportCollection $collection): static {
		$this->view->setReportCollection($collection);
		return $this;
	}

	public function setWrapperFrom(string $pathToWrapper): static {
		if (!is_file($pathToWrapper)) {
			throw new FileNotFoundException($pathToWrapper);
		}
		$wrapper = file_get_contents($pathToWrapper);
		if (!$wrapper) {
			throw new FileException('No content in wrapper file ' . $pathToWrapper . '.');
		}
		$this->wrapper = $wrapper;
		return $this;
	}

	protected function createNavigation(): Navigation {
		$round  = Lemuria::Calendar()->Round();
		$nextAt = $this->getCurrentRoundTimestamp() + $this->turnInterval;
		return new Navigation($round, $nextAt, $this->firstRound);
	}

	protected function getCurrentRoundTimestamp(): int {
		if ($this->turnTimestamp < self::MINIMUM_TIMESTAMP || $this->turnInterval < self::MINIMUM_INTERVAL) {
			return time();
		}

		$currentRound    = Lemuria::Calendar()->Round();
		$turnsSinceFirst = $currentRound - $this->firstRound;
		return $this->turnTimestamp + $turnsSinceFirst * $this->turnInterval;
	}
}
