<?php
declare(strict_types = 1);

use Lemuria\Alpha\AlphaSimulator;
use Lemuria\Engine\Message;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;

require_once __DIR__ . '/../vendor/autoload.php';

if ($argc < 2) {
	file_put_contents('php://stderr', 'Fehler: Keine Partei-UUID angegeben.' . PHP_EOL);
	exit(1);
}

try {
	$simulator = new AlphaSimulator();
	$uuid      = $argv[1];
	$party     = Lemuria::Registry()->find($uuid);
	if ($party instanceof Party) {
		$orders = __DIR__ . '/../storage/orders/' . $simulator->Round() . '/' . $uuid . '.order';
		$move   = new CommandFile($orders);
		$report = $simulator->simulate($move)->getReport($party);
		$eol    = false;
		foreach ($report as $entity => $messages) {
			if ($eol) {
				echo PHP_EOL;
			}
			if ($entity) {
				echo $entity . ':' . PHP_EOL;
			} else {
				echo 'Parteimeldungen:' . PHP_EOL;
			}
			foreach ($messages as $message/* @var Message $message */) {
				echo $simulator->render($message) . PHP_EOL;
			}
			$eol = true;
		}
		exit(0);
	}
} catch (\Throwable $e) {
	file_put_contents('php://stderr', (string)$e);
	exit(2);
}

file_put_contents('php://stderr', 'Fehler: Partei nicht gefunden.' . PHP_EOL);
exit(1);
