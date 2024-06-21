<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Event;

use Lemuria\Engine\Fantasya\Event\Administrator;
use Lemuria\Engine\Fantasya\Factory\ReflectionTrait;
use Lemuria\Exception\FileNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Storage\Ini\Section;
use Lemuria\Storage\Ini\SectionList;
use Lemuria\Storage\IniProvider;

final readonly class AdministratorInjector
{
	use ReflectionTrait;

	private const string EVENT_NAMESPACE = 'Lemuria\\Engine\\Fantasya\\Event\\Administrator\\';

	private const string ROUND = 'runde';

	private SectionList $list;

	private int $round;

	private array $values;

	public function __construct(string $resourcePath) {
		if (!is_file($resourcePath)) {
			throw new FileNotFoundException($resourcePath);
		}
		$provider    = new IniProvider(dirname($resourcePath));
		$this->list  = $provider->read(basename($resourcePath));
		$this->round = Lemuria::Calendar()->Round();
	}

	public function inject(Administrator $administrator): void {
		foreach ($this->list as $section) {
			$this->values = $this->getValues($section);
			if ($this->isDue()) {
				$class = self::EVENT_NAMESPACE . $section->Name();
				$this->validateEventClass($class);
				$administrator->add($class, $this->getOptions());
			}
		}
	}

	private function getValues(Section $section): array {
		$values = [];
		foreach ($section->Values() as $name => $value) {
			if (mb_strtolower($name) === self::ROUND) {
				$values[self::ROUND] = (int)(string)$value;
			} else {
				$values[$name] = $value;
			}
		}
		return $values;
	}

	private function isDue(): bool {
		if (isset($this->values[self::ROUND])) {
			return $this->values[self::ROUND] === $this->round;
		}
		return true;
	}

	private function getOptions(): ?array {
		$options = [];
		foreach ($this->values as $name => $value) {
			if ($name !== self::ROUND) {
				$value  = (string)$value;
				$number = (int)$value;
				if ((string)$number === $value) {
					$value = $number;
				} elseif ($value === 'false') {
					$value = false;
				} elseif ($value === 'true') {
					$value = true;
				} elseif (preg_match('/^(\\d+)[.,](\\d+)$/', $value, $matches) === 1) {
					$value = (float)($matches[1] . '.' . $matches[2]);
				}
				$options[$name] = $value;
			}
		}
		return empty($options) ? null : $options;
	}
}
