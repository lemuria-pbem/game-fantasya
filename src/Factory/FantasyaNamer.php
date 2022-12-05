<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Factory;

use Lemuria\Engine\Fantasya\Factory\Namer\DefaultNamer;
use Lemuria\Exception\LemuriaException;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Landscape;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;

class FantasyaNamer extends DefaultNamer
{
	use BuilderTrait;

	protected const GENERATOR = __DIR__ . '/../../bin/name-generator.sh';

	/**
	 * @var array<string>
	 */
	protected array $names = [];

	protected int $next = 0;

	protected int $count = 0;

	protected ?Landscape $ocean = null;

	protected function location(Region $region): string {
		if (!$this->ocean) {
			$this->ocean = self::createLandscape(Ocean::class);
		}
		if ($region->Landscape() === $this->ocean) {
			return self::dictionary()->get('landscape.' . $this->ocean);
		}
		return $this->next();
	}

	protected function next(): string {
		if ($this->next >= $this->count) {
			$this->createNames();
		}
		return $this->names[$this->next++];
	}

	protected function createNames(): void {
		if (!@exec(self::GENERATOR, $output)) {
			throw new LemuriaException('Could not run name generator script.');
		}
		$this->names = $output;
		$this->count = count($output);
		$this->next  = 0;
	}
}
