<?php
declare(strict_types = 1);
namespace Lemuria\Test;

use Lemuria\Engine\Lemuria\Factory\DefaultReport;
use Lemuria\Engine\Lemuria\SingletonCatalog as EngineSingletonCatalog;
use Lemuria\Engine\Report;
use Lemuria\Factory\DefaultBuilder;
use Lemuria\Model\Builder;
use Lemuria\Model\Calendar;
use Lemuria\Model\Catalog;
use Lemuria\Model\Config;
use Lemuria\Model\Calendar\BaseCalendar;
use Lemuria\Model\Game;
use Lemuria\Model\Lemuria\Factory\DefaultCatalog;
use Lemuria\Model\Lemuria\SingletonCatalog as ModelSingletonCatalog;
use Lemuria\Model\World;
use Lemuria\Model\World\OctagonalMap;

class TestConfig implements Config
{
	public function __construct(private int $round = 0) {
	}

	public function Builder(): Builder {
		$builder = new DefaultBuilder();
		return $builder->register(new ModelSingletonCatalog())->register(new EngineSingletonCatalog());
	}

	public function Catalog(): Catalog {
		return new DefaultCatalog();
	}

	public function Calendar(): Calendar {
		return new BaseCalendar();
	}

	public function Game(): Game {
		return new TestGame($this->round);
	}

	public function Report(): Report {
		return new DefaultReport();
	}

	public function World(): World {
		return new OctagonalMap();
	}

	public function getPathToLog(): string {
		return realpath(__DIR__ . '/../storage/log') . DIRECTORY_SEPARATOR . 'lemuria.log';
	}
}
