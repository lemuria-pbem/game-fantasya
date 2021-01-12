<?php
declare(strict_types = 1);
namespace Lemuria\Test;

use Lemuria\Engine\Lemuria\Storage\LemuriaGame;
use Lemuria\Storage\FileProvider;

final class TestGame extends LemuriaGame
{
	public function __construct(private int $round) {
		parent::__construct();
	}

	/**
	 * @return array(string=>string)
	 */
	protected function getLoadStorage(): array {
		$dir     = $this->round > 0 ? 'save/' . $this->round : 'game';
		$storage = [FileProvider::DEFAULT => new FileProvider(__DIR__ . '/../storage/' . $dir)];
		return array_merge($storage, $this->getStringStorage());
	}

	/**
	 * @return array(string=>string)
	 */
	protected function getSaveStorage(): array {
		return [FileProvider::DEFAULT => new FileProvider(__DIR__ . '/../storage/save/' . ($this->round + 1))];
	}
}
