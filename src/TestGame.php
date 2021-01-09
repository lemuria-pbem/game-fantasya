<?php
declare(strict_types = 1);
namespace Lemuria\Test;

use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Model\Lemuria\Storage\JsonGame;

final class TestGame extends JsonGame
{
	public function __construct(private int $round) {
		parent::__construct();
	}

	public function getMessages(): array {
		return [];
	}

	protected function getLoadStorage(): string {
		if ($this->round > 0) {
			$dir  = __DIR__ . '/../storage/save/' . $this->round;
			$path = realpath($dir);
			if (!$path) {
				throw new DirectoryNotFoundException($dir);
			}
			return $path;
		}
		return realpath(__DIR__ . '/../storage/game');
	}

	protected function getSaveStorage(): string {
		return realpath(__DIR__ . '/../storage/save') . DIRECTORY_SEPARATOR . ($this->round + 1);
	}
}
