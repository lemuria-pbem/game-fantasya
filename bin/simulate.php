<?php
declare(strict_types = 1);

use Lemuria\Engine\Move\CommandFile;
use Lemuria\Game\Fantasya\FantasyaSimulator;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Profiler;

require_once __DIR__ . '/../vendor/autoload.php';

putenv(Profiler::LEMURIA_ZERO_HOUR . '=' . microtime(true));

const STDOUT_ONLY = '--stdout-only';

if ($argc < 2) {
	file_put_contents('php://stderr', 'Fehler: Keine Partei-UUID angegeben.' . PHP_EOL);
	exit(1);
}

$uuid       = $argv[1];
$stdoutOnly = false;
if ($argc === 3) {
	$stdoutOnly = $argv[2] === STDOUT_ONLY;
	if ($uuid === STDOUT_ONLY) {
		if ($stdoutOnly) {
			file_put_contents('php://stderr', 'Fehler: Keine Partei-UUID angegeben.' . PHP_EOL);
			exit(1);
		}
		$uuid       = $argv[2];
		$stdoutOnly = true;
	}
	if (!$stdoutOnly) {
		file_put_contents('php://stderr', 'Fehler: Unbekannter Parameter angegeben.' . PHP_EOL);
		exit(1);
	}
} elseif ($argc > 3) {
	file_put_contents('php://stderr', 'Fehler: Zu viele Parameter angegeben.' . PHP_EOL);
	exit(1);
}

try {
	$simulator = new FantasyaSimulator();
	$party     = Lemuria::Registry()->find($uuid);
	if ($party instanceof Party) {
		$orders = __DIR__ . '/../storage/orders/' . $simulator->Round() . '/' . $uuid . '.order';
		$move   = new CommandFile($orders);
		$report = $simulator->pick($party)->simulate($move)->getReport($party);
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
			foreach ($messages as $message) {
				echo $simulator->render($message) . PHP_EOL;
			}
			$eol = true;
		}
		$simulator->logProfiler();
		exit(0);
	}
} catch (\Throwable $e) {
	if ($stdoutOnly) {
		echo 'Simulation abgebrochen: ' . $e->getMessage() . PHP_EOL . $e->getFile() . ':' . $e->getLine();
	} else {
		file_put_contents('php://stderr', (string)$e);
	}
	exit(2);
}

if ($stdoutOnly) {
	echo 'Fehler: Partei nicht gefunden.' . PHP_EOL;
} else {
	file_put_contents('php://stderr', 'Fehler: Partei nicht gefunden.' . PHP_EOL);
}
exit(1);
