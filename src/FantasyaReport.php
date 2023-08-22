<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\Factory\PartyUnica;
use Lemuria\Engine\Fantasya\Message\Filter\PartyAnnouncementFilter;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\DebugFilter;
use Lemuria\Engine\Message\Filter\CompositeFilter;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Game\Fantasya\Renderer\Magellan\FantasyaHeader;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Profiler;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Renderer\Text\BattleLogWriter;
use Lemuria\Renderer\Text\HerbalBookWriter;
use Lemuria\Renderer\Text\HtmlWriter;
use Lemuria\Renderer\Text\OrderWriter;
use Lemuria\Renderer\Text\SpellBookWriter;
use Lemuria\Renderer\Text\TextWriter;
use Lemuria\Renderer\Text\UnicumWriter;
use Lemuria\Renderer\Text\Wrapper\FileWrapper;
use Lemuria\Version\Module;
use Lemuria\Version\VersionFinder;

class FantasyaReport
{
	private const HTML_WRAPPER = __DIR__ . '/../resources/turn.html';

	private const HTML_WRAPPER_DEBUG = __DIR__ . '/../resources/turn.debug.html';

	protected readonly string $storage;

	protected FantasyaConfig $config;

	protected int $nextRound;

	protected array $received = [];

	protected readonly bool $profilingEnabled;

	private readonly array $debugParties;

	public function __construct() {
		$this->storage = realpath(__DIR__ . '/../storage');
		if (!$this->storage) {
			throw new DirectoryNotFoundException($this->storage);
		}
		$this->config           = new FantasyaConfig($this->storage);
		$this->nextRound        = $this->config[LemuriaConfig::ROUND];
		$this->profilingEnabled = $this->config[FantasyaConfig::ENABLE_PROFILING];
		$this->debugParties     = array_fill_keys($this->config[FantasyaConfig::DEBUG_PARTIES], true);
	}

	public function init(): static {
		Lemuria::init($this->config);
		if ($this->profilingEnabled) {
			Lemuria::Log()->debug('Profiler [' . Profiler::RECORD_ZERO . ']: ' . Lemuria::Profiler()->getRecord(Profiler::RECORD_ZERO));
		}

		Lemuria::load();
		Lemuria::Log()->debug('Generating reports for round ' . $this->nextRound . '.');

		$version                 = Lemuria::Version();
		$versionFinder           = new VersionFinder(__DIR__ . '/../vendor/lemuria-pbem/engine-fantasya');
		$version[Module::Engine] = $versionFinder->get();
		$versionFinder           = new VersionFinder(__DIR__ . '/..');
		$version[Module::Game]   = $versionFinder->get();

		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordAndLog('FantasyaReport_load');
		}
		return $this;
	}

	public function createReports(): static {
		$directory = realpath($this->storage . '/turn');
		if (!$directory) {
			throw new DirectoryNotFoundException($directory);
		}
		$directory  .= DIRECTORY_SEPARATOR . $this->nextRound;
		$pathFactory = new FantasyaPathFactory($directory);
		$version     = Lemuria::Version();
		$header      = new FantasyaHeader();

		$p          = 0;
		$hasVersion = false;
		foreach (Party::all() as $party) {
			if (!$this->generateReportFor($party)) {
				continue;
			}

			$id       = $party->Id();
			$received = $this->received[$id->Id()] ?? null;
			$isPlayer = $party->Type() === Type::Player;
			$filter   = $this->getMessageFilter($party);
			$pathFactory->setPrefix((string)$id);
			Lemuria::Log()->debug('Using ' . get_class($filter) . ' for report messages of Party ' . $id . '.');

			$writer = new MagellanWriter($pathFactory);
			if (!$hasVersion) {
				$version[Module::Renderers] = $writer->getVersion();
			}
			if ($isPlayer) {
				$writer->setHeader($header)->setFilter($filter)->render($id);
			}

			$writer = new HtmlWriter($pathFactory);
			if (!$hasVersion) {
				$version[Module::Renderers] = $writer->getVersion();
			}
			$wrapper = new FileWrapper($this->getHtmlWrap());
			$writer->add($wrapper->setWriter($writer)->setReceived($received))->setFilter($filter)->render($id);

			if ($isPlayer) {
				$writer = new TextWriter($pathFactory);
				$writer->setFilter($filter)->render($id);
				$writer = new OrderWriter($pathFactory);
				$writer->render($id);
				if ($party->SpellBook()->count() > 0) {
					$writer = new SpellBookWriter($pathFactory);
					$writer->render($id);
				}
				if ($party->HerbalBook()->count() > 0) {
					$writer = new HerbalBookWriter($pathFactory);
					$writer->render($id);
				}
				$unica = new PartyUnica($party);
				foreach ($unica->Treasury() as $unicum) {
					$writer = new UnicumWriter($pathFactory);
					$writer->render($unicum->Id());
				}
			}

			$writer = new BattleLogWriter($pathFactory);
			$writer->render($id);

			$p++;
			$hasVersion = true;
			if ($this->profilingEnabled) {
				Lemuria::Profiler()->recordAndLog('FantasyaReport_report-' . $id);
			}
		}
		Lemuria::Log()->debug('Report generation finished for ' . $p . ' parties.');

		return $this;
	}

	public function logProfiler(): void {
		if ($this->profilingEnabled) {
			Lemuria::Profiler()->recordTotal();
			Lemuria::Profiler()->logTotalPeak();
		}
	}

	protected function generateReportFor(Party $party): bool {
		return !$party->hasRetired() || $party->Retirement() >= $this->nextRound;
	}

	protected function getHtmlWrap(): string {
		if (Lemuria::FeatureFlag()->IsDevelopment()) {
			if (is_file(self::HTML_WRAPPER_DEBUG)) {
				return self::HTML_WRAPPER_DEBUG;
			}
		}
		return self::HTML_WRAPPER;
	}

	protected function getMessageFilter(Party $party): Filter {
		$id = $party->Uuid();
		if (isset($this->debugParties[$id])) {
			return new PartyAnnouncementFilter();
		}
		$filter = new CompositeFilter();
		return $filter->add(new DebugFilter())->add(new PartyAnnouncementFilter());
	}
}
