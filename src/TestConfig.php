<?php
declare(strict_types = 1);
namespace Lemuria\Test;

use JetBrains\PhpStorm\Pure;

use Lemuria\Engine\Lemuria\Factory\DefaultReport;
use Lemuria\Engine\Lemuria\SingletonCatalog as EngineSingletonCatalog;
use Lemuria\Engine\Report;
use Lemuria\Exception\LemuriaException;
use Lemuria\Factory\DefaultBuilder;
use Lemuria\Model\Builder;
use Lemuria\Model\Calendar;
use Lemuria\Model\Catalog;
use Lemuria\Model\Config;
use Lemuria\Model\Calendar\BaseCalendar;
use Lemuria\Model\Exception\JsonException;
use Lemuria\Model\Game;
use Lemuria\Model\Lemuria\Factory\DefaultCatalog;
use Lemuria\Model\Lemuria\SingletonCatalog as ModelSingletonCatalog;
use Lemuria\Model\Lemuria\Storage\JsonProvider;
use Lemuria\Model\World;
use Lemuria\Model\World\OctagonalMap;

final class TestConfig implements \ArrayAccess, Config
{
	public const ROUND = 'round';

	private const DIRECTORY = __DIR__ . '/../storage';

	private const FILE = 'config.json';

	private const DEFAULTS = [
		self::ROUND => 0
	];

	private JsonProvider $file;

	private ?array $config;

	/**
	 * @throws JsonException
	 */
	public function __construct() {
		$this->file = new JsonProvider(self::DIRECTORY);
		if ($this->file->exists(self::FILE)) {
			$this->config = $this->file->read(self::FILE);
		} else {
			$this->config = self::DEFAULTS;
		}
	}

	/**
	 * @throws JsonException
	 */
	function __destruct() {
		$this->file->write(self::FILE, $this->config);
	}

	/**
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists(mixed $offset): bool {
		return isset($this->config[$offset]);
	}

	public function offsetGet(mixed $offset): mixed {
		if (!$this->offsetExists($offset)) {
			throw new LemuriaException("No config value for '" . $offset ."'.");
		}
		return $this->config[$offset];
	}

	public function offsetSet(mixed $offset, mixed $value) {
		if (!$this->offsetExists($offset)) {
			throw new LemuriaException("Invalid config setting '" . $offset . "'.");
		}
		$this->config[$offset] = $value;
	}

	public function offsetUnset(mixed $offset): void {
		if (!$this->offsetExists($offset)) {
			throw new LemuriaException("No config value for '" . $offset ."'.");
		}
		$this->config[$offset] = self::DEFAULTS[$offset];
	}

	public function Builder(): Builder {
		$builder = new DefaultBuilder();
		return $builder->register(new ModelSingletonCatalog())->register(new EngineSingletonCatalog());
	}

	public function Catalog(): Catalog {
		return new DefaultCatalog();
	}

	#[Pure] public function Calendar(): Calendar {
		return new BaseCalendar();
	}

	public function Game(): Game {
		return new TestGame($this[self::ROUND]);
	}

	public function Report(): Report {
		return new DefaultReport();
	}

	#[Pure] public function World(): World {
		return new OctagonalMap();
	}

	#[Pure] public function getPathToLog(): string {
		return realpath(__DIR__ . '/../storage/log') . DIRECTORY_SEPARATOR . 'lemuria.log';
	}
}
