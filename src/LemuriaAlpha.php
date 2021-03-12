<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Renderer\Text\HtmlWriter;
use Lemuria\Renderer\Text\OrderWriter;
use Lemuria\Renderer\Text\TextWriter;
use Lemuria\Renderer\Text\Wrapper\FileWrapper;

final class LemuriaAlpha
{
	private const HTML_WRAPPER = __DIR__ . '/../resources/turn.html';

	private LemuriaConfig $config;

	private int $round;

	private string $storage;

	private LemuriaTurn $turn;

	public function __construct() {
		$this->storage = realpath(__DIR__ . '/../storage');
		if (!$this->storage) {
			throw new DirectoryNotFoundException($this->storage);
		}
		$this->config = new LemuriaConfig($this->storage);
		$this->round  = $this->config[LemuriaConfig::ROUND];
	}

	public function init(): self {
		Lemuria::init($this->config);
		Lemuria::Log()->debug('Turn starts.', ['config' => $this->config]);
		Lemuria::load();
		Lemuria::Log()->debug('Evaluating round ' . Lemuria::Calendar()->Round() . '.', ['calendar' => Lemuria::Calendar()]);
		$this->turn = new LemuriaTurn();

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
		foreach ($parties as $path) {
			$this->turn->add(new CommandFile($path));
		}

		return $this;
	}

	public function evaluate(): self {
		Lemuria::Log()->debug('Starting evaluation.');
		$this->turn->evaluate();
		Lemuria::Calendar()->nextRound();

		return $this;
	}

	public function finish(): self {
		Lemuria::save();
		$this->config[LemuriaConfig::ROUND] = ++$this->round;
		$this->config[LemuriaConfig::MDD]   = time();
		Lemuria::Log()->debug('Turn ended.');

		return $this;
	}

	public function createReports(): void {
		Lemuria::Log()->debug('Generating reports.', ['config' => $this->config]);
		$dir = realpath($this->storage . '/turn');
		if (!$dir) {
			throw new DirectoryNotFoundException($dir);
		}
		$dir .= DIRECTORY_SEPARATOR . $this->round;
		if (!is_dir($dir)) {
			mkdir($dir);
			chmod($dir, 0775);
		}
		$p = 0;
		foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
			$id   = $party->Id();
			$name = (string)$id;

			$htmlPath = $dir . DIRECTORY_SEPARATOR . $name . '.html';
			$writer   = new HtmlWriter($htmlPath);
			$writer->add(new FileWrapper(self::HTML_WRAPPER))->render($id);

			$txtPath = $dir . DIRECTORY_SEPARATOR . $name . '.txt';
			$writer  = new TextWriter($txtPath);
			$writer->render($id);

			$crPath = $dir . DIRECTORY_SEPARATOR . $name . '.cr';
			$writer = new MagellanWriter($crPath);
			$writer->render($id);

			$orderPath = $dir . DIRECTORY_SEPARATOR . $name . '.orders.txt';
			$writer = new OrderWriter($orderPath);
			$writer->render($id);

			$p++;
		}
		Lemuria::Log()->debug('Report generation finished for ' . $p . ' parties.');
	}

	public function getReports(): array {
		$dir = realpath($this->storage . '/turn/' . $this->round);
		if (!$dir) {
			throw new DirectoryNotFoundException($dir);
		}
		return glob($dir . DIRECTORY_SEPARATOR . '*.html');
	}
}
