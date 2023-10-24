<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Engine\Fantasya\LemuriaTurn;
use Lemuria\Engine\Fantasya\Script;
use Lemuria\Lemuria;

class FantasyaScripts
{
	private array $scripts = [];

	public function __construct(private readonly LemuriaTurn $turn) {
	}

	public function load(): void {
		foreach (Lemuria::Game()->getScripts() as $file => $data) {
			$script = new Script($file, $data);
			$this->turn->addScript($script);
			$this->scripts[] = $script;
		}
	}

	public function save(): void {
		$scripts = [];
		foreach ($this->scripts as $script) {
			$data = $script->Data();
			if ($data->count() > 0) {
				$scripts[$script->File()] = $data;
			}
		}
	}
}
