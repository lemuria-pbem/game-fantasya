<?php
declare (strict_types = 1);

use Lemuria\Engine\Lemuria\LemuriaTurn;
use Lemuria\Engine\Lemuria\Message\LemuriaMessage;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Exception\FileException;
use Lemuria\Exception\LemuriaException;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Test\TestConfig;

try {
	require realpath(__DIR__ . '/../vendor/autoload.php');

	$config = new TestConfig();
	$round  = $config[TestConfig::ROUND];

	$dir    = __DIR__ . '/../storage/orders/' . ($round + 1);
	$orders = realpath($dir);
	if (!$orders) {
		throw new DirectoryNotFoundException($dir);
	}
	$parties = glob($orders . DIRECTORY_SEPARATOR . '*.txt');

	try {
		Lemuria::init($config);
		Lemuria::Log()->debug('Turn starts.', ['timestamp' => date('r')]);
		Lemuria::load();
		Lemuria::Log()->debug('Evaluating round ' . Lemuria::Calendar()->Round() . '.', ['calendar' => Lemuria::Calendar()]);
		$turn = new LemuriaTurn();
		foreach ($parties as $path) {
			$turn->add(new CommandFile($path));
		}
		$turn->evaluate();
		Lemuria::Calendar()->nextRound();


		foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
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
			$config[TestConfig::ROUND] = ++$round;
			$config[TestConfig::MDD]   = time();
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
