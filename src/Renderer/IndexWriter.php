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

	protected FantasyaPathFactory $pathFactory;

	protected View $view;

	protected readonly VersionTag $version;

	protected string $wrapper = self::BODY;

	public function __construct(PathFactory $pathFactory) {
		/** @noinspection PhpConditionAlreadyCheckedInspection */
		if ($pathFactory instanceof FantasyaPathFactory) {
			$this->pathFactory = $pathFactory;
		} else {
			throw new LemuriaException('This writer needs the ' . getClass(FantasyaPathFactory::class) . '.');
		}
		$this->view    = new View();
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
}
