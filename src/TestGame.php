<?php
declare(strict_types = 1);
namespace Lemuria\Test;

use Lemuria\Model\Lemuria\Storage\JsonGame;

final class TestGame extends JsonGame
{
	protected function getLoadStorage(): string {
		return realpath(__DIR__ . '/../storage/game');
	}

	protected function getSaveStorage(): string {
		return realpath(__DIR__ . '/../storage/save') . DIRECTORY_SEPARATOR . time();
	}
}
