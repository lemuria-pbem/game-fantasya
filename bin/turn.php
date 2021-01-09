<?php
declare (strict_types = 1);

use Lemuria\Engine\Lemuria\LemuriaTurn;
use Lemuria\Engine\Lemuria\Message\LemuriaMessage;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Exception\LemuriaException;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Exception\FileException;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Test\TestConfig;

try {
	require realpath(__DIR__ . '/../vendor/autoload.php');

	$round = 0;

	$dir    = __DIR__ . '/../storage/orders/' . ($round + 1);
	$orders = realpath($dir);
	if (!$orders) {
		throw new DirectoryNotFoundException($dir);
	}
	$parties = glob($orders . DIRECTORY_SEPARATOR . '*.txt');

	try {
		Lemuria::init(new TestConfig($round));
		Lemuria::Log()->debug('Turn starts.', ['timestamp' => date('r')]);
		Lemuria::load();
		Lemuria::Log()->debug('Evaluating round ' . Lemuria::Calendar()->Round() . '.', ['calendar' => Lemuria::Calendar()]);
		$turn = new LemuriaTurn();
		foreach ($parties as $path) {
			$turn->add(new CommandFile($path));
		}
		$turn->evaluate();
		Lemuria::Calendar()->nextRound();

		foreach ($parties as $path) {
			$file  = basename($path);
			$id    = substr($file, 0, strpos($file, '.'));
			$party = Party::get(Id::fromId($id));
			Lemuria::Log()->debug('Logging game messages for party ' . $party . ':');
			foreach (Lemuria::Report()->getAll($party) as $message/* @var LemuriaMessage $message */) {
				Lemuria::Log()->log($message->Level(), $message);
			}
			foreach ($party->People() as $unit) {
				foreach (Lemuria::Report()->getAll($unit) as $message/* @var LemuriaMessage $message */) {
					Lemuria::Log()->log($message->Level(), $message);
				}
			}
		}

		try {
			Lemuria::save();
			Lemuria::Log()->debug('Turn ended.');
			exit(0);
		} catch (FileException $e) {
			Lemuria::Log()->alert('Saving data failed. Turn ended.');
			exit(1);
		}
	} catch (FileException $e) {
		Lemuria::Log()->alert('Loading data failed. Turn aborted.');
		exit(2);
	}
} catch (LemuriaException $e) {
	Lemuria::Log()->emergency('Implementation error.', ['exception' => $e]);
	exit(3);
} catch (Throwable $e) {
	Lemuria::Log()->alert('Runtime error.', ['exception' => $e]);
	exit(4);
}
