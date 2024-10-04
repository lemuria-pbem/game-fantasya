<?php
declare(strict_types = 1);

use Lemuria\Engine\Move\CommandFile;
use Lemuria\Game\Fantasya\Simulation\BootOption;
use Lemuria\Game\Fantasya\Simulation\FantasyaSimulator;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;

require_once __DIR__ . '/../vendor/autoload.php';

const BUILD_CACHE   = '--build-cache';
const CLEAR_CACHE   = '--clear-cache';
const FAST          = '--fast';
const HELP          = ['-h', '--help'];
const STDOUT_ONLY   = '--stdout-only';
const ALL_KNOWN     = [BUILD_CACHE, CLEAR_CACHE, FAST, STDOUT_ONLY];
const WITH_ARGUMENT = [FAST, STDOUT_ONLY];
const NO_ARGUMENT   = [BUILD_CACHE, CLEAR_CACHE, ...HELP];

$option     = $argc > 1 && str_starts_with($argv[1], '-') ? $argv[1] : null;
$stdoutOnly = $option === STDOUT_ONLY ? true : $argc > 2 && $argv[2] === STDOUT_ONLY;
$max        = $option ? ($option === STDOUT_ONLY ? 2 : ($stdoutOnly ? 3 : 2)) : ($stdoutOnly ? 2 : 1);
$uuid       = $argc > $max ? $argv[$max] : null;

$isHelp      = $argc < 2 || in_array($option, HELP);
$tooManyArgs = $argc > ++$max;
$unknownOpt  = $option && !in_array($option, ALL_KNOWN);
$needsArg    = $option && in_array($option, WITH_ARGUMENT) && !$uuid;
$noArgOption = $option && in_array($option, NO_ARGUMENT) && $uuid;
$tooManyOpts = in_array($option, NO_ARGUMENT) && $stdoutOnly;
$abort       = ($isHelp || $tooManyArgs || $unknownOpt || $needsArg || $noArgOption || $tooManyOpts);

if ($abort) {
	if ($isHelp) {
		echo 'Aufruf: php simulate.php [option] [UUID]' . PHP_EOL;
		echo PHP_EOL;
		echo 'Optionen:' . PHP_EOL;
		echo '  --build-cache   Cache neu aufbauen' . PHP_EOL;
		echo '  --clear-cache   Cache löschen' . PHP_EOL;
		echo '  --fast          Simulation für UUID aus Cache starten' . PHP_EOL;
		echo '  --help, -h      Hilfe anzeigen' . PHP_EOL;
		echo '  --stdout-only   Fehlermeldungen aus STDERR unterdrücken' . PHP_EOL;
		exit(0);
	}
	file_put_contents('php://stderr', 'Eingabefehler! Hilfe aufrufen mit --help.' . PHP_EOL);
	exit(1);
}

try {
	switch ($option) {
		case BUILD_CACHE :
			FantasyaSimulator::boot(BootOption::BuildCache);
			echo 'Simulation cache created.' . PHP_EOL;
			exit(0);
		case CLEAR_CACHE :
			$result = FantasyaSimulator::boot(BootOption::ClearCache);
			if ($result) {
				echo 'Simulation cache cleared.' . PHP_EOL;
				exit(0);
			}
			echo 'Simulation cache file not found.' . PHP_EOL;
			exit(1);
		case FAST :
			$simulator = FantasyaSimulator::boot(BootOption::FromCache);
			break;
		default :
			$simulator = FantasyaSimulator::boot();
	}

	$party = Lemuria::Registry()->find($uuid);
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
